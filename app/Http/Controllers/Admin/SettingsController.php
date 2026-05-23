<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScriptInjection;
use App\Services\CMS\SettingsService;
use Illuminate\Http\Request;

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
        $this->settings->updateGroup($group, $request->all());
        return back()->with('success', 'Settings saved!');
    }

    public function scripts()
    {
        $scripts = ScriptInjection::orderBy('location')->get();
        return view('admin.settings.scripts', compact('scripts'));
    }

    public function storeScript(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:100',
            'provider'  => 'nullable|string|max:100',
            'location'  => 'required|in:head,body_start,body_end',
            'code'      => 'required|string',
            'is_active' => 'boolean',
        ]);

        ScriptInjection::updateOrCreate(['name' => $data['name']], $data);
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
        $menus = \App\Models\NavigationMenu::with(['items.children'])->get();
        return view('admin.settings.menus', compact('menus'));
    }

    public function updateMenus(Request $request)
    {
        // Menu update logic handled via AJAX in the view
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
