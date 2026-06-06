<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScriptInjection;
use App\Services\CMS\SettingsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function __construct(private SettingsService $settings) {}

    public function index()
    {
        $groups = ['general', 'homepage', 'social', 'seo', 'footer', 'integrations'];
        $all = [];
        foreach ($groups as $group) {
            $all[$group] = $this->settings->group($group);
        }
        return view('admin.settings.index', compact('all', 'groups'));
    }

    public function update(Request $request, string $group)
    {
        $data = $request->except(['_token', '_method']);

        // Handle file uploads — store to public disk so asset() URLs work
        foreach ($request->allFiles() as $key => $file) {
            if ($file && $file->isValid()) {
                // Delete old file if one exists
                $old = \App\Models\SiteSetting::get($key);
                if ($old && Storage::disk('public')->exists($old)) {
                    Storage::disk('public')->delete($old);
                }
                $data[$key] = $file->store("settings/{$group}", 'public');
            }
        }

        $this->settings->updateGroup($group, $data);
        return back()->with('success', 'Settings saved!');
    }

    public function scripts()
    {
        try {
            $scripts = ScriptInjection::orderBy('location')->orderBy('name')->get();
        } catch (\Throwable $e) {
            $scripts = collect();
        }
        return view('admin.settings.scripts', compact('scripts'));
    }

    public function storeScript(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:100',
            'provider'  => 'nullable|string|max:100',
            'location'  => 'required|in:head,body_start,body_end',
            'code'      => 'required|string',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        ScriptInjection::updateOrCreate(
            ['name' => $data['name']],
            $data
        );
        return back()->with('success', 'Script saved!');
    }

    public function destroyScript(int $id)
    {
        ScriptInjection::findOrFail($id)->delete();
        return back()->with('success', 'Script deleted!');
    }

    public function seo()
    {
        $seo = $this->settings->group('seo');
        return view('admin.settings.seo', compact('seo'));
    }

    public function updateSeo(Request $request)
    {
        $this->settings->updateGroup('seo', $request->all());
        return back()->with('success', 'SEO settings updated!');
    }

    public function menus()
    {
        $menus = \App\Models\NavigationMenu::with(['items' => fn($q) => $q->orderBy('sort_order'), 'items.children' => fn($q) => $q->orderBy('sort_order')])->get();
        $pages = \App\Models\Page::where('status', 'published')->orderBy('title')->get();
        return view('admin.settings.menus', compact('menus', 'pages'));
    }

    public function storeMenu(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'location' => 'required|string|max:100',
        ]);
        \App\Models\NavigationMenu::create(array_merge($data, ['is_active' => true]));
        return back()->with('success', 'Menu created.');
    }

    public function storeMenuItem(Request $request)
    {
        $data = $request->validate([
            'menu_id'    => 'required|exists:navigation_menus,id',
            'parent_id'  => 'nullable|exists:navigation_items,id',
            'label'      => 'required|string|max:100',
            'url'        => 'nullable|string|max:500',
            'page_id'    => 'nullable|exists:pages,id',
            'target'     => 'in:_self,_blank',
            'is_active'  => 'nullable|boolean',
        ]);

        // If a page was selected, generate its URL
        if (!empty($data['page_id'])) {
            $page = \App\Models\Page::find($data['page_id']);
            $data['url'] = '/' . $page->slug;
        }
        unset($data['page_id']);

        $max = \App\Models\NavigationItem::where('menu_id', $data['menu_id'])
            ->whereNull('parent_id')->max('sort_order') ?? 0;
        $data['sort_order']  = $max + 1;
        $data['is_active']   = true;
        $data['is_external'] = ($data['target'] ?? '_self') === '_blank';

        \App\Models\NavigationItem::create($data);
        return back()->with('success', 'Menu item added.');
    }

    public function updateMenuItem(Request $request, \App\Models\NavigationItem $item)
    {
        $data = $request->validate([
            'label'      => 'required|string|max:100',
            'url'        => 'nullable|string|max:500',
            'target'     => 'in:_self,_blank',
            'is_active'  => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);
        $data['is_external'] = ($data['target'] ?? '_self') === '_blank';
        $data['is_active'] = $request->boolean('is_active');
        $item->update($data);
        return back()->with('success', 'Menu item updated.');
    }

    public function destroyMenuItem(\App\Models\NavigationItem $item)
    {
        $item->children()->delete();
        $item->delete();
        return back()->with('success', 'Menu item removed.');
    }

    public function reorderMenuItems(Request $request)
    {
        $request->validate(['items' => 'required|array', 'items.*.id' => 'required|integer', 'items.*.sort_order' => 'required|integer']);
        foreach ($request->items as $row) {
            \App\Models\NavigationItem::where('id', $row['id'])->update(['sort_order' => $row['sort_order']]);
        }
        return response()->json(['success' => true]);
    }

    public function updateIntegrations(Request $request)
    {
        $fields = [
            'PAYSTACK_PUBLIC_KEY'  => $request->paystack_public_key,
            'PAYSTACK_SECRET_KEY'  => $request->paystack_secret_key,
            'MAIL_MAILER'          => $request->mail_mailer,
            'MAIL_HOST'            => $request->mail_host,
            'MAIL_PORT'            => $request->mail_port,
            'MAIL_USERNAME'        => $request->mail_username,
            'MAIL_PASSWORD'        => $request->mail_password,
            'MAIL_ENCRYPTION'      => $request->mail_encryption,
            'MAIL_FROM_ADDRESS'    => $request->mail_from_address,
            'MAIL_FROM_NAME'       => $request->mail_from_name,
        ];

        foreach ($fields as $key => $value) {
            if (!is_null($value) && $value !== '') {
                $this->setEnvValue($key, $value);
            }
        }

        return back()->with('success', 'Integration settings saved! Restart the server if needed.');
    }

    private function setEnvValue(string $key, string $value): void
    {
        $path = base_path('.env');
        $content = file_get_contents($path);
        $value = str_contains($value, ' ') ? '"' . $value . '"' : $value;

        if (strpos($content, "{$key}=") !== false) {
            $content = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $content);
        } else {
            $content .= "\n{$key}={$value}";
        }

        file_put_contents($path, $content);
    }
}
