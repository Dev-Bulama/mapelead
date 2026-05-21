<?php
namespace App\Services\CMS;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    public function all(): array
    {
        return SiteSetting::all()->groupBy('group')->map(fn($items) =>
            $items->pluck('value', 'key')
        )->toArray();
    }

    public function group(string $group): array
    {
        return SiteSetting::getGroup($group);
    }

    public function get(string $key, $default = null): mixed
    {
        return SiteSetting::get($key, $default);
    }

    public function updateGroup(string $group, array $data): void
    {
        foreach ($data as $key => $value) {
            SiteSetting::set($key, $value);
        }
        Cache::tags(['settings'])->flush();
    }
}
