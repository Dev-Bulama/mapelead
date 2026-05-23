<?php

namespace Database\Seeders;

use App\Models\HeroBanner;
use App\Models\Page;
use Illuminate\Database\Seeder;

class CmsContentSeeder extends Seeder
{
    public function run(): void
    {
        // ── Hero Banners ──────────────────────────────────────────────────────
        HeroBanner::truncate();

        $banners = [
            [
                'title'              => "Africa's #1 Tech Training Platform",
                'subtitle'           => 'From Zero to Job-Ready in Weeks',
                'description'        => 'Join over 10,000 students mastering Cybersecurity, Data Science, Cloud Computing, and Full-Stack Development through structured, industry-led programs.',
                'badge_text'         => '🎓 Certified Programs',
                'primary_btn_text'   => 'Explore Courses',
                'primary_btn_url'    => '/courses',
                'secondary_btn_text' => 'View Admissions',
                'secondary_btn_url'  => '/admissions',
                'image'              => null,
                'is_active'          => true,
                'sort_order'         => 1,
            ],
            [
                'title'              => 'Cybersecurity Certification Program',
                'subtitle'           => 'Become a Certified Ethical Hacker',
                'description'        => 'Master penetration testing, vulnerability assessment, and network defense. Earn globally recognized CEH certification and fast-track your career.',
                'badge_text'         => '🔒 Most Popular',
                'primary_btn_text'   => 'Enroll Now',
                'primary_btn_url'    => '/courses',
                'secondary_btn_text' => 'Learn More',
                'secondary_btn_url'  => '/courses',
                'image'              => null,
                'is_active'          => true,
                'sort_order'         => 2,
            ],
            [
                'title'              => 'Data Science & AI Bootcamp',
                'subtitle'           => 'Turn Data into Decisions',
                'description'        => 'Learn Python, machine learning, deep learning, and data visualization. Build real projects that impress employers. Flexible payment plans available.',
                'badge_text'         => '🤖 AI-Powered Curriculum',
                'primary_btn_text'   => 'Start Learning',
                'primary_btn_url'    => '/courses',
                'secondary_btn_text' => 'Download Brochure',
                'secondary_btn_url'  => '/contact',
                'image'              => null,
                'is_active'          => true,
                'sort_order'         => 3,
            ],
            [
                'title'              => 'Pay As You Learn',
                'subtitle'           => 'Flexible Installment Plans — No Barrier to Education',
                'description'        => 'We believe financial constraints should never stop you from building a tech career. Choose from full payment or spread the cost over 2–12 monthly installments.',
                'badge_text'         => '💳 Installments Available',
                'primary_btn_text'   => 'See Pricing',
                'primary_btn_url'    => '/courses',
                'secondary_btn_text' => 'Contact Admissions',
                'secondary_btn_url'  => '/contact',
                'image'              => null,
                'is_active'          => true,
                'sort_order'         => 4,
            ],
        ];

        foreach ($banners as $banner) {
            HeroBanner::create($banner);
        }

        // ── CMS Pages ─────────────────────────────────────────────────────────
        if (class_exists(\App\Models\Page::class)) {
            $pages = [
                [
                    'title'        => 'About Us',
                    'slug'         => 'about',
                    'content'      => '<h2>About Mapelead</h2><p>Mapelead is a leading technology training institution committed to bridging the digital skills gap across Africa. Founded with the mission to make world-class tech education accessible and affordable, we offer certified programs in Cybersecurity, Data Science, Cloud Computing, and Full-Stack Development.</p><h3>Our Mission</h3><p>To empower the next generation of African tech professionals with industry-relevant skills, hands-on experience, and global certification.</p><h3>Our Vision</h3><p>To be Africa\'s most trusted and impactful technology training hub, producing globally competitive tech talent.</p>',
                    'meta_title'   => 'About Mapelead — Africa\'s #1 Tech Training Platform',
                    'meta_description' =>'Learn about Mapelead\'s mission to empower African tech professionals through world-class training in Cybersecurity, Data Science, and more.',
                    'status'       => 'published',
                ],
                [
                    'title'        => 'Privacy Policy',
                    'slug'         => 'privacy-policy',
                    'content'      => '<h2>Privacy Policy</h2><p>Last updated: ' . now()->format('F j, Y') . '</p><p>At Mapelead, we take your privacy seriously. This policy explains how we collect, use, and protect your personal information when you use our platform.</p><h3>Information We Collect</h3><ul><li>Name, email address, and phone number when you register</li><li>Payment information (processed securely via Paystack)</li><li>Course progress and quiz results</li><li>IP address and browser information for security</li></ul><h3>How We Use Your Information</h3><ul><li>To provide and improve our educational services</li><li>To process payments and issue certificates</li><li>To send course updates and important notifications</li><li>To comply with legal obligations</li></ul><h3>Contact Us</h3><p>For privacy questions, email: privacy@mapelead.org</p>',
                    'meta_title'   => 'Privacy Policy — Mapelead',
                    'meta_description' =>'Read Mapelead\'s privacy policy to understand how we collect and protect your personal information.',
                    'status'       => 'published',
                ],
                [
                    'title'        => 'Terms of Service',
                    'slug'         => 'terms',
                    'content'      => '<h2>Terms of Service</h2><p>Last updated: ' . now()->format('F j, Y') . '</p><p>By accessing or using Mapelead\'s platform, you agree to be bound by these terms of service.</p><h3>Enrollment & Payment</h3><p>All course enrollments are subject to payment of the applicable fees. For installment plans, failure to make scheduled payments may result in temporary suspension of course access until outstanding balance is cleared.</p><h3>Intellectual Property</h3><p>All course materials, videos, and content are the exclusive property of Mapelead and may not be copied, distributed, or resold without written permission.</p><h3>Certificates</h3><p>Certificates are issued upon successful completion of all course requirements including minimum attendance and passing quiz scores.</p><h3>Refund Policy</h3><p>Refund requests must be submitted within 7 days of enrollment. After 7 days, no refunds will be issued.</p>',
                    'meta_title'   => 'Terms of Service — Mapelead',
                    'meta_description' =>'Review Mapelead\'s terms of service for enrollment, payments, intellectual property, and certificates.',
                    'status'       => 'published',
                ],
                [
                    'title'        => 'FAQs',
                    'slug'         => 'faqs',
                    'content'      => '<h2>Frequently Asked Questions</h2><h3>How do I enroll in a course?</h3><p>Click "Enroll Now" on any course page, select your payment plan, and complete the Paystack checkout. You\'ll receive instant access upon successful payment.</p><h3>Do you offer installment payment plans?</h3><p>Yes! All paid courses offer flexible installment plans ranging from 2 to 12 monthly payments. You choose your down payment and schedule.</p><h3>Will I get a certificate?</h3><p>Yes. Upon completing all course modules and passing the required assessments, you\'ll receive a digitally verified certificate of completion.</p><h3>Can I access courses on mobile?</h3><p>Yes. Our platform is fully responsive and works on all devices — mobile, tablet, and desktop.</p><h3>What if I need help?</h3><p>Our support team is available via the Help & Support section in your dashboard. We typically respond within 24 hours.</p>',
                    'meta_title'   => 'FAQs — Mapelead',
                    'meta_description' =>'Find answers to common questions about Mapelead courses, enrollment, payments, and certificates.',
                    'status'       => 'published',
                ],
            ];

            foreach ($pages as $page) {
                \App\Models\Page::updateOrCreate(['slug' => $page['slug']], $page);
            }
        }

        $this->command->info('✓ CMS content seeded: ' . count($banners) . ' hero banners, ' . (isset($pages) ? count($pages) : 0) . ' pages');
    }
}
