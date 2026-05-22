<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $fillable = [
        'name', 'slug', 'subject', 'body_html', 'body_text',
        'available_variables', 'is_system', 'is_active', 'last_used_at',
    ];

    protected $casts = [
        'available_variables' => 'array',
        'is_system' => 'boolean',
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
    ];

    public static function findBySlug(string $slug): ?self
    {
        return static::where('slug', $slug)->where('is_active', true)->first();
    }

    public function render(array $variables = []): string
    {
        $html = $this->body_html;
        foreach ($variables as $key => $value) {
            $html = str_replace('{{' . $key . '}}', $value, $html);
            $html = str_replace('{{ ' . $key . ' }}', $value, $html);
        }
        return $html;
    }

    public function renderSubject(array $variables = []): string
    {
        $subject = $this->subject;
        foreach ($variables as $key => $value) {
            $subject = str_replace('{{' . $key . '}}', $value, $subject);
            $subject = str_replace('{{ ' . $key . ' }}', $value, $subject);
        }
        return $subject;
    }
}
