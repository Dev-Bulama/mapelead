<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SiteSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General
            ['group' => 'general', 'key' => 'site_name',        'value' => 'MapeLeads',                 'type' => 'text',    'label' => 'Site Name'],
            ['group' => 'general', 'key' => 'site_tagline',     'value' => 'Africa\'s Premier Tech Training Platform', 'type' => 'text', 'label' => 'Tagline'],
            ['group' => 'general', 'key' => 'site_email',       'value' => 'hello@mapelead.org',         'type' => 'text',    'label' => 'Contact Email'],
            ['group' => 'general', 'key' => 'site_phone',       'value' => '+234 800 000 0000',          'type' => 'text',    'label' => 'Phone'],
            ['group' => 'general', 'key' => 'site_address',     'value' => 'Lagos, Nigeria',             'type' => 'text',    'label' => 'Address'],
            ['group' => 'general', 'key' => 'site_logo',        'value' => '',                           'type' => 'image',   'label' => 'Logo'],
            ['group' => 'general', 'key' => 'site_favicon',     'value' => '',                           'type' => 'image',   'label' => 'Favicon'],
            ['group' => 'general', 'key' => 'site_currency',    'value' => 'NGN',                        'type' => 'text',    'label' => 'Currency'],
            ['group' => 'general', 'key' => 'currency_symbol',  'value' => '₦',                         'type' => 'text',    'label' => 'Currency Symbol'],
            // Social
            ['group' => 'social',  'key' => 'facebook_url',     'value' => '',                           'type' => 'text',    'label' => 'Facebook URL'],
            ['group' => 'social',  'key' => 'twitter_url',      'value' => '',                           'type' => 'text',    'label' => 'Twitter/X URL'],
            ['group' => 'social',  'key' => 'instagram_url',    'value' => '',                           'type' => 'text',    'label' => 'Instagram URL'],
            ['group' => 'social',  'key' => 'linkedin_url',     'value' => '',                           'type' => 'text',    'label' => 'LinkedIn URL'],
            ['group' => 'social',  'key' => 'youtube_url',      'value' => '',                           'type' => 'text',    'label' => 'YouTube URL'],
            ['group' => 'social',  'key' => 'whatsapp_number',  'value' => '',                           'type' => 'text',    'label' => 'WhatsApp Number'],
            // Homepage
            ['group' => 'homepage','key' => 'hero_badge',       'value' => '#1 Tech Training in Africa', 'type' => 'text',    'label' => 'Hero Badge Text'],
            ['group' => 'homepage','key' => 'hero_title',       'value' => 'Build In-Demand Tech Skills That Get You Hired', 'type' => 'text', 'label' => 'Hero Title'],
            ['group' => 'homepage','key' => 'hero_subtitle',    'value' => 'Join 10,000+ graduates who transformed their careers through our industry-led courses in Data Science, Cybersecurity, Product Design & more.', 'type' => 'text', 'label' => 'Hero Subtitle'],
            ['group' => 'homepage','key' => 'hero_cta_primary', 'value' => 'Explore Courses',            'type' => 'text',    'label' => 'Hero Primary CTA'],
            ['group' => 'homepage','key' => 'hero_cta_secondary','value' => 'Download Brochure',         'type' => 'text',    'label' => 'Hero Secondary CTA'],
            ['group' => 'homepage','key' => 'stat_students',    'value' => '10,000+',                    'type' => 'text',    'label' => 'Students Count'],
            ['group' => 'homepage','key' => 'stat_courses',     'value' => '50+',                        'type' => 'text',    'label' => 'Courses Count'],
            ['group' => 'homepage','key' => 'stat_instructors', 'value' => '25+',                        'type' => 'text',    'label' => 'Instructors Count'],
            ['group' => 'homepage','key' => 'stat_placement',   'value' => '92%',                        'type' => 'text',    'label' => 'Job Placement Rate'],
            // SEO
            ['group' => 'seo',     'key' => 'meta_title',       'value' => 'MapeLeads – Africa\'s Premier Tech Training Platform', 'type' => 'text', 'label' => 'Default Meta Title'],
            ['group' => 'seo',     'key' => 'meta_description', 'value' => 'Join 10,000+ students learning Data Science, Cybersecurity, Product Design and more at MapeLeads.', 'type' => 'text', 'label' => 'Default Meta Description'],
            ['group' => 'seo',     'key' => 'google_analytics', 'value' => '',                           'type' => 'text',    'label' => 'Google Analytics ID'],
            ['group' => 'seo',     'key' => 'meta_pixel',       'value' => '',                           'type' => 'text',    'label' => 'Meta Pixel ID'],
            // Footer
            ['group' => 'footer',  'key' => 'footer_about',     'value' => 'MapeLeads empowers African professionals with world-class tech skills through practical, industry-relevant training programs.', 'type' => 'text', 'label' => 'Footer About Text'],
            ['group' => 'footer',  'key' => 'footer_copyright', 'value' => '© 2025 MapeLeads. All rights reserved.', 'type' => 'text', 'label' => 'Copyright Text'],
        ];

        foreach ($settings as $setting) {
            DB::table('site_settings')->updateOrInsert(
                ['key' => $setting['key']],
                array_merge($setting, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}
