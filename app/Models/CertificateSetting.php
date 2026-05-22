<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CertificateSetting extends Model
{
    protected $fillable = [
        'logo_path', 'signature_path', 'seal_path',
        'header_text', 'body_text', 'footer_text',
        'background_color', 'primary_color', 'text_color',
        'layout', 'qr_enabled', 'organization_name',
        'signatory_name', 'signatory_title',
    ];

    protected $casts = ['qr_enabled' => 'boolean'];

    public static function instance(): self
    {
        return static::firstOrCreate([], [
            'header_text' => 'Certificate of Completion',
            'background_color' => '#ffffff',
            'primary_color' => '#14215B',
            'text_color' => '#1a1a1a',
            'layout' => 'landscape',
            'qr_enabled' => true,
        ]);
    }
}
