<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplatesSeeder extends Seeder
{
    // Shared variables available in all templates
    private array $sharedVariables = [
        'student_name',
        'balance',
        'due_date',
        'course_name',
        'admission_number',
        'amount_due',
    ];

    public function run(): void
    {
        $templates = [
            $this->paymentReminderTemplate(),
            $this->portalLockedTemplate(),
            $this->certificateIssuedTemplate(),
            $this->enrollmentConfirmationTemplate(),
            $this->installmentDueTemplate(),
        ];

        foreach ($templates as $template) {
            EmailTemplate::updateOrCreate(
                ['slug' => $template['slug']],
                array_merge($template, [
                    'is_system'           => true,
                    'is_active'           => true,
                    'available_variables' => $this->sharedVariables,
                ])
            );
        }

        $this->command->info('EmailTemplatesSeeder: ' . count($templates) . ' email templates seeded.');
    }

    // ─── Template Definitions ─────────────────────────────────────────────────

    private function paymentReminderTemplate(): array
    {
        return [
            'slug'      => 'payment_reminder',
            'name'      => 'Payment Reminder',
            'subject'   => 'Payment Reminder: {{course_name}} installment due {{due_date}}',
            'body_html' => <<<'HTML'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Reminder</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f6f9; margin: 0; padding: 0; }
        .email-wrapper { max-width: 620px; margin: 40px auto; background-color: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .email-header { background: linear-gradient(135deg, #14215B 0%, #1e3a8a 100%); padding: 40px 40px 30px; text-align: center; }
        .email-header img { height: 40px; margin-bottom: 16px; }
        .email-header h1 { color: #ffffff; font-size: 22px; font-weight: 700; margin: 0; letter-spacing: 0.5px; }
        .email-header .subtitle { color: #93c5fd; font-size: 14px; margin-top: 6px; }
        .email-body { padding: 40px; color: #374151; line-height: 1.7; }
        .email-body p { margin: 0 0 18px; font-size: 15px; }
        .alert-box { background-color: #fef3c7; border-left: 4px solid #f59e0b; border-radius: 6px; padding: 18px 20px; margin: 24px 0; }
        .alert-box p { margin: 0; font-size: 14px; color: #92400e; }
        .details-table { width: 100%; border-collapse: collapse; margin: 24px 0; }
        .details-table tr td { padding: 12px 16px; font-size: 14px; border-bottom: 1px solid #e5e7eb; }
        .details-table tr td:first-child { color: #6b7280; font-weight: 500; width: 45%; }
        .details-table tr td:last-child { color: #111827; font-weight: 600; }
        .details-table tr:last-child td { border-bottom: none; }
        .cta-section { text-align: center; margin: 32px 0; }
        .cta-button { display: inline-block; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: #ffffff; text-decoration: none; font-size: 16px; font-weight: 700; padding: 14px 36px; border-radius: 8px; letter-spacing: 0.3px; }
        .email-footer { background-color: #f9fafb; border-top: 1px solid #e5e7eb; padding: 24px 40px; text-align: center; }
        .email-footer p { color: #9ca3af; font-size: 12px; margin: 4px 0; }
        .email-footer a { color: #6b7280; text-decoration: none; }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-header">
            <h1>Mapelead Academy</h1>
            <div class="subtitle">Payment Reminder</div>
        </div>
        <div class="email-body">
            <p>Dear <strong>{{student_name}}</strong>,</p>
            <p>We hope your learning journey on <strong>{{course_name}}</strong> is going well. This is a friendly reminder that an installment payment is due soon.</p>

            <div class="alert-box">
                <p><strong>Action Required:</strong> Please ensure your payment is completed by <strong>{{due_date}}</strong> to maintain uninterrupted access to your course portal.</p>
            </div>

            <table class="details-table">
                <tr>
                    <td>Admission Number</td>
                    <td>{{admission_number}}</td>
                </tr>
                <tr>
                    <td>Course</td>
                    <td>{{course_name}}</td>
                </tr>
                <tr>
                    <td>Amount Due</td>
                    <td>₦{{amount_due}}</td>
                </tr>
                <tr>
                    <td>Payment Due Date</td>
                    <td>{{due_date}}</td>
                </tr>
                <tr>
                    <td>Outstanding Balance</td>
                    <td>₦{{balance}}</td>
                </tr>
            </table>

            <div class="cta-section">
                <a href="#" class="cta-button">Make Payment Now</a>
            </div>

            <p>If you have already made this payment, please disregard this message. If you are experiencing any difficulties, please contact our support team immediately.</p>
            <p>Thank you for being part of the Mapelead community.</p>
            <p>Warm regards,<br><strong>The Mapelead Team</strong></p>
        </div>
        <div class="email-footer">
            <p><strong>Mapelead Academy</strong> — Empowering Africa's Digital Future</p>
            <p><a href="#">support@mapelead.com</a> | <a href="#">www.mapelead.com</a></p>
            <p>© 2026 Mapelead. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
HTML,
        ];
    }

    private function portalLockedTemplate(): array
    {
        return [
            'slug'      => 'portal_locked',
            'name'      => 'Portal Access Suspended',
            'subject'   => 'Your access to {{course_name}} has been suspended',
            'body_html' => <<<'HTML'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Access Suspended</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f6f9; margin: 0; padding: 0; }
        .email-wrapper { max-width: 620px; margin: 40px auto; background-color: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .email-header { background: linear-gradient(135deg, #991b1b 0%, #dc2626 100%); padding: 40px 40px 30px; text-align: center; }
        .email-header h1 { color: #ffffff; font-size: 22px; font-weight: 700; margin: 0; }
        .email-header .subtitle { color: #fca5a5; font-size: 14px; margin-top: 6px; }
        .email-body { padding: 40px; color: #374151; line-height: 1.7; }
        .email-body p { margin: 0 0 18px; font-size: 15px; }
        .alert-box { background-color: #fee2e2; border-left: 4px solid #dc2626; border-radius: 6px; padding: 18px 20px; margin: 24px 0; }
        .alert-box p { margin: 0; font-size: 14px; color: #7f1d1d; }
        .steps-list { background-color: #f9fafb; border-radius: 8px; padding: 20px 24px; margin: 24px 0; }
        .steps-list h3 { margin: 0 0 14px; font-size: 15px; color: #111827; }
        .steps-list ol { margin: 0; padding-left: 20px; }
        .steps-list ol li { font-size: 14px; color: #374151; margin-bottom: 8px; line-height: 1.6; }
        .details-table { width: 100%; border-collapse: collapse; margin: 24px 0; }
        .details-table tr td { padding: 12px 16px; font-size: 14px; border-bottom: 1px solid #e5e7eb; }
        .details-table tr td:first-child { color: #6b7280; font-weight: 500; width: 45%; }
        .details-table tr td:last-child { color: #111827; font-weight: 600; }
        .details-table tr:last-child td { border-bottom: none; }
        .cta-section { text-align: center; margin: 32px 0; }
        .cta-button { display: inline-block; background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%); color: #ffffff; text-decoration: none; font-size: 16px; font-weight: 700; padding: 14px 36px; border-radius: 8px; }
        .email-footer { background-color: #f9fafb; border-top: 1px solid #e5e7eb; padding: 24px 40px; text-align: center; }
        .email-footer p { color: #9ca3af; font-size: 12px; margin: 4px 0; }
        .email-footer a { color: #6b7280; text-decoration: none; }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-header">
            <h1>Mapelead Academy</h1>
            <div class="subtitle">Portal Access Suspended</div>
        </div>
        <div class="email-body">
            <p>Dear <strong>{{student_name}}</strong>,</p>
            <p>We regret to inform you that your access to the <strong>{{course_name}}</strong> student portal has been <strong>temporarily suspended</strong> due to an overdue payment.</p>

            <div class="alert-box">
                <p><strong>Outstanding Balance:</strong> ₦{{balance}}<br>
                Your portal will remain locked until the outstanding balance is cleared. Please act promptly to avoid any disruption to your learning.</p>
            </div>

            <table class="details-table">
                <tr>
                    <td>Admission Number</td>
                    <td>{{admission_number}}</td>
                </tr>
                <tr>
                    <td>Course</td>
                    <td>{{course_name}}</td>
                </tr>
                <tr>
                    <td>Amount Overdue</td>
                    <td>₦{{amount_due}}</td>
                </tr>
                <tr>
                    <td>Outstanding Balance</td>
                    <td>₦{{balance}}</td>
                </tr>
            </table>

            <div class="steps-list">
                <h3>How to Restore Your Access</h3>
                <ol>
                    <li>Log in to your student portal using your email and password.</li>
                    <li>Navigate to <strong>Payments</strong> in your dashboard.</li>
                    <li>Complete the outstanding payment of <strong>₦{{amount_due}}</strong>.</li>
                    <li>Your access will be restored automatically within a few minutes of payment confirmation.</li>
                    <li>If your access is not restored within 30 minutes, contact our support team.</li>
                </ol>
            </div>

            <div class="cta-section">
                <a href="#" class="cta-button">Restore My Access</a>
            </div>

            <p>If you believe this suspension is an error, or if you need assistance with payment arrangements, please contact our support team immediately at <a href="mailto:support@mapelead.com">support@mapelead.com</a>.</p>
            <p>We value your commitment to learning and look forward to welcoming you back.</p>
            <p>Regards,<br><strong>Mapelead Student Support Team</strong></p>
        </div>
        <div class="email-footer">
            <p><strong>Mapelead Academy</strong> — Empowering Africa's Digital Future</p>
            <p><a href="#">support@mapelead.com</a> | <a href="#">www.mapelead.com</a></p>
            <p>© 2026 Mapelead. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
HTML,
        ];
    }

    private function certificateIssuedTemplate(): array
    {
        return [
            'slug'      => 'certificate_issued',
            'name'      => 'Certificate Issued',
            'subject'   => 'Congratulations! Your certificate for {{course_name}} is ready',
            'body_html' => <<<'HTML'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate Issued</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f6f9; margin: 0; padding: 0; }
        .email-wrapper { max-width: 620px; margin: 40px auto; background-color: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .email-header { background: linear-gradient(135deg, #065f46 0%, #059669 100%); padding: 40px 40px 30px; text-align: center; }
        .email-header h1 { color: #ffffff; font-size: 22px; font-weight: 700; margin: 0; }
        .email-header .subtitle { color: #a7f3d0; font-size: 14px; margin-top: 6px; }
        .email-header .badge { display: inline-block; background-color: rgba(255,255,255,0.2); color: #ffffff; font-size: 32px; padding: 12px; border-radius: 50%; margin-bottom: 16px; }
        .email-body { padding: 40px; color: #374151; line-height: 1.7; }
        .email-body p { margin: 0 0 18px; font-size: 15px; }
        .success-box { background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%); border: 1px solid #6ee7b7; border-radius: 10px; padding: 24px; margin: 24px 0; text-align: center; }
        .success-box h2 { color: #065f46; font-size: 20px; margin: 0 0 8px; }
        .success-box p { color: #047857; margin: 0; font-size: 14px; }
        .details-table { width: 100%; border-collapse: collapse; margin: 24px 0; }
        .details-table tr td { padding: 12px 16px; font-size: 14px; border-bottom: 1px solid #e5e7eb; }
        .details-table tr td:first-child { color: #6b7280; font-weight: 500; width: 45%; }
        .details-table tr td:last-child { color: #111827; font-weight: 600; }
        .details-table tr:last-child td { border-bottom: none; }
        .cta-section { text-align: center; margin: 32px 0; }
        .cta-button { display: inline-block; background: linear-gradient(135deg, #059669 0%, #047857 100%); color: #ffffff; text-decoration: none; font-size: 16px; font-weight: 700; padding: 14px 36px; border-radius: 8px; margin: 6px; }
        .cta-button-secondary { display: inline-block; background: transparent; color: #059669; text-decoration: none; font-size: 15px; font-weight: 600; padding: 14px 36px; border-radius: 8px; border: 2px solid #059669; margin: 6px; }
        .share-section { background-color: #f9fafb; border-radius: 8px; padding: 20px 24px; margin: 24px 0; text-align: center; }
        .share-section p { margin: 0 0 12px; font-size: 14px; color: #6b7280; }
        .email-footer { background-color: #f9fafb; border-top: 1px solid #e5e7eb; padding: 24px 40px; text-align: center; }
        .email-footer p { color: #9ca3af; font-size: 12px; margin: 4px 0; }
        .email-footer a { color: #6b7280; text-decoration: none; }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-header">
            <div class="badge">🎓</div>
            <h1>Mapelead Academy</h1>
            <div class="subtitle">Certificate of Completion</div>
        </div>
        <div class="email-body">
            <p>Dear <strong>{{student_name}}</strong>,</p>

            <div class="success-box">
                <h2>Congratulations on Your Achievement!</h2>
                <p>You have successfully completed <strong>{{course_name}}</strong> and your certificate is now ready for download.</p>
            </div>

            <p>We are incredibly proud of your dedication and hard work. Completing <strong>{{course_name}}</strong> is a significant milestone in your professional development journey, and you should be very proud of this accomplishment.</p>

            <table class="details-table">
                <tr>
                    <td>Student Name</td>
                    <td>{{student_name}}</td>
                </tr>
                <tr>
                    <td>Admission Number</td>
                    <td>{{admission_number}}</td>
                </tr>
                <tr>
                    <td>Course Completed</td>
                    <td>{{course_name}}</td>
                </tr>
                <tr>
                    <td>Completion Date</td>
                    <td>{{due_date}}</td>
                </tr>
            </table>

            <div class="cta-section">
                <a href="#" class="cta-button">Download Certificate</a>
                <a href="#" class="cta-button-secondary">Verify Certificate</a>
            </div>

            <div class="share-section">
                <p><strong>Share your achievement!</strong> Add your Mapelead certificate to your LinkedIn profile and let your network know about your new skills.</p>
                <a href="#" style="color: #0077b5; font-weight: 600; text-decoration: none; font-size: 14px;">Add to LinkedIn Profile →</a>
            </div>

            <p>Your journey with Mapelead does not have to end here. Explore our other courses and continue growing your skills in cybersecurity, cloud, data science, and more.</p>
            <p>Congratulations once again on this fantastic achievement!</p>
            <p>Warm regards,<br><strong>The Mapelead Team</strong></p>
        </div>
        <div class="email-footer">
            <p><strong>Mapelead Academy</strong> — Empowering Africa's Digital Future</p>
            <p><a href="#">support@mapelead.com</a> | <a href="#">www.mapelead.com</a></p>
            <p>© 2026 Mapelead. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
HTML,
        ];
    }

    private function enrollmentConfirmationTemplate(): array
    {
        return [
            'slug'      => 'enrollment_confirmation',
            'name'      => 'Enrollment Confirmation',
            'subject'   => 'Welcome to {{course_name}}! Your enrollment is confirmed',
            'body_html' => <<<'HTML'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrollment Confirmation</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f6f9; margin: 0; padding: 0; }
        .email-wrapper { max-width: 620px; margin: 40px auto; background-color: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .email-header { background: linear-gradient(135deg, #14215B 0%, #1e40af 100%); padding: 40px 40px 30px; text-align: center; }
        .email-header h1 { color: #ffffff; font-size: 22px; font-weight: 700; margin: 0; }
        .email-header .subtitle { color: #93c5fd; font-size: 14px; margin-top: 6px; }
        .welcome-badge { background-color: rgba(255,255,255,0.15); display: inline-block; color: #ffffff; font-size: 13px; font-weight: 600; padding: 6px 16px; border-radius: 20px; margin-bottom: 16px; letter-spacing: 1px; text-transform: uppercase; }
        .email-body { padding: 40px; color: #374151; line-height: 1.7; }
        .email-body p { margin: 0 0 18px; font-size: 15px; }
        .welcome-box { background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border: 1px solid #93c5fd; border-radius: 10px; padding: 24px; margin: 24px 0; }
        .welcome-box h2 { color: #1e40af; font-size: 18px; margin: 0 0 8px; }
        .welcome-box p { color: #1e3a8a; margin: 0; font-size: 14px; }
        .details-table { width: 100%; border-collapse: collapse; margin: 24px 0; border-radius: 8px; overflow: hidden; border: 1px solid #e5e7eb; }
        .details-table tr td { padding: 12px 16px; font-size: 14px; border-bottom: 1px solid #e5e7eb; }
        .details-table tr td:first-child { color: #6b7280; font-weight: 500; width: 45%; background-color: #f9fafb; }
        .details-table tr td:last-child { color: #111827; font-weight: 600; }
        .details-table tr:last-child td { border-bottom: none; }
        .steps-list { background-color: #f9fafb; border-radius: 8px; padding: 24px; margin: 24px 0; }
        .steps-list h3 { margin: 0 0 16px; font-size: 16px; color: #111827; }
        .step { display: flex; align-items: flex-start; margin-bottom: 14px; }
        .step-number { background: linear-gradient(135deg, #14215B, #1e40af); color: #ffffff; font-size: 12px; font-weight: 700; width: 26px; height: 26px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-right: 12px; margin-top: 2px; }
        .step-text { font-size: 14px; color: #374151; line-height: 1.5; }
        .cta-section { text-align: center; margin: 32px 0; }
        .cta-button { display: inline-block; background: linear-gradient(135deg, #14215B 0%, #1e40af 100%); color: #ffffff; text-decoration: none; font-size: 16px; font-weight: 700; padding: 14px 36px; border-radius: 8px; letter-spacing: 0.3px; }
        .support-box { background-color: #fefce8; border: 1px solid #fde68a; border-radius: 8px; padding: 16px 20px; margin: 24px 0; }
        .support-box p { margin: 0; font-size: 13px; color: #92400e; }
        .email-footer { background-color: #f9fafb; border-top: 1px solid #e5e7eb; padding: 24px 40px; text-align: center; }
        .email-footer p { color: #9ca3af; font-size: 12px; margin: 4px 0; }
        .email-footer a { color: #6b7280; text-decoration: none; }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-header">
            <div class="welcome-badge">Enrollment Confirmed</div>
            <h1>Mapelead Academy</h1>
            <div class="subtitle">Welcome to your learning journey</div>
        </div>
        <div class="email-body">
            <p>Dear <strong>{{student_name}}</strong>,</p>

            <div class="welcome-box">
                <h2>🎉 Welcome to {{course_name}}!</h2>
                <p>Your enrollment has been confirmed. We are thrilled to have you join us. Your Mapelead portal is now active and ready for you to begin learning.</p>
            </div>

            <p>Please save your course details below for your records:</p>

            <table class="details-table">
                <tr>
                    <td>Full Name</td>
                    <td>{{student_name}}</td>
                </tr>
                <tr>
                    <td>Admission Number</td>
                    <td>{{admission_number}}</td>
                </tr>
                <tr>
                    <td>Course Enrolled</td>
                    <td>{{course_name}}</td>
                </tr>
                <tr>
                    <td>Course Start Date</td>
                    <td>{{due_date}}</td>
                </tr>
                <tr>
                    <td>Amount Paid</td>
                    <td>₦{{amount_due}}</td>
                </tr>
            </table>

            <div class="steps-list">
                <h3>Getting Started — Next Steps</h3>
                <div class="step">
                    <div class="step-number">1</div>
                    <div class="step-text"><strong>Log in to your portal</strong> using your email address and the password you set during registration.</div>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <div class="step-text"><strong>Complete your profile</strong> — add a photo and update your bio so your instructor and classmates can get to know you.</div>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <div class="step-text"><strong>Access your course</strong> from the dashboard under "My Courses" and start with the free preview lessons.</div>
                </div>
                <div class="step">
                    <div class="step-number">4</div>
                    <div class="step-text"><strong>Join the class WhatsApp group</strong> — your course coordinator will share the link during the first class.</div>
                </div>
            </div>

            <div class="cta-section">
                <a href="#" class="cta-button">Access My Course Portal</a>
            </div>

            <div class="support-box">
                <p><strong>Need help?</strong> Our support team is available Monday to Saturday, 9am – 6pm (WAT). Email us at <a href="mailto:support@mapelead.com">support@mapelead.com</a> or call +234 800 MAPELEAD.</p>
            </div>

            <p>We are excited to be part of your growth journey. Let's build something great together!</p>
            <p>Warm regards,<br><strong>The Mapelead Team</strong></p>
        </div>
        <div class="email-footer">
            <p><strong>Mapelead Academy</strong> — Empowering Africa's Digital Future</p>
            <p><a href="#">support@mapelead.com</a> | <a href="#">www.mapelead.com</a></p>
            <p>© 2026 Mapelead. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
HTML,
        ];
    }

    private function installmentDueTemplate(): array
    {
        return [
            'slug'      => 'installment_due',
            'name'      => 'Installment Due Today',
            'subject'   => 'ACTION REQUIRED: Payment due today for {{course_name}}',
            'body_html' => <<<'HTML'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installment Due Today</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f6f9; margin: 0; padding: 0; }
        .email-wrapper { max-width: 620px; margin: 40px auto; background-color: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .email-header { background: linear-gradient(135deg, #7c2d12 0%, #ea580c 100%); padding: 40px 40px 30px; text-align: center; }
        .email-header h1 { color: #ffffff; font-size: 22px; font-weight: 700; margin: 0; }
        .email-header .subtitle { color: #fed7aa; font-size: 14px; margin-top: 6px; }
        .urgent-badge { background-color: rgba(255,255,255,0.2); display: inline-block; color: #ffffff; font-size: 12px; font-weight: 700; padding: 6px 16px; border-radius: 20px; margin-bottom: 16px; letter-spacing: 2px; text-transform: uppercase; }
        .email-body { padding: 40px; color: #374151; line-height: 1.7; }
        .email-body p { margin: 0 0 18px; font-size: 15px; }
        .urgent-box { background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%); border: 2px solid #ea580c; border-radius: 10px; padding: 24px; margin: 24px 0; text-align: center; }
        .urgent-box .amount { font-size: 36px; font-weight: 800; color: #c2410c; margin: 8px 0; }
        .urgent-box .label { font-size: 13px; color: #9a3412; text-transform: uppercase; letter-spacing: 1px; }
        .alert-box { background-color: #fef3c7; border-left: 4px solid #f59e0b; border-radius: 6px; padding: 18px 20px; margin: 24px 0; }
        .alert-box p { margin: 0; font-size: 14px; color: #78350f; }
        .details-table { width: 100%; border-collapse: collapse; margin: 24px 0; }
        .details-table tr td { padding: 12px 16px; font-size: 14px; border-bottom: 1px solid #e5e7eb; }
        .details-table tr td:first-child { color: #6b7280; font-weight: 500; width: 45%; }
        .details-table tr td:last-child { color: #111827; font-weight: 600; }
        .details-table tr:last-child td { border-bottom: none; }
        .consequences-list { background-color: #fef2f2; border-radius: 8px; padding: 20px 24px; margin: 24px 0; }
        .consequences-list h3 { margin: 0 0 12px; font-size: 14px; color: #991b1b; text-transform: uppercase; letter-spacing: 0.5px; }
        .consequences-list ul { margin: 0; padding-left: 20px; }
        .consequences-list ul li { font-size: 13px; color: #7f1d1d; margin-bottom: 6px; }
        .cta-section { text-align: center; margin: 32px 0; }
        .cta-button { display: inline-block; background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%); color: #ffffff; text-decoration: none; font-size: 17px; font-weight: 800; padding: 16px 40px; border-radius: 8px; letter-spacing: 0.5px; }
        .email-footer { background-color: #f9fafb; border-top: 1px solid #e5e7eb; padding: 24px 40px; text-align: center; }
        .email-footer p { color: #9ca3af; font-size: 12px; margin: 4px 0; }
        .email-footer a { color: #6b7280; text-decoration: none; }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-header">
            <div class="urgent-badge">⚡ Action Required</div>
            <h1>Mapelead Academy</h1>
            <div class="subtitle">Payment Due Today</div>
        </div>
        <div class="email-body">
            <p>Dear <strong>{{student_name}}</strong>,</p>
            <p>This is an urgent notice. Your installment payment for <strong>{{course_name}}</strong> is due <strong>today</strong>.</p>

            <div class="urgent-box">
                <div class="label">Amount Due Today</div>
                <div class="amount">₦{{amount_due}}</div>
                <div class="label">Due Date: {{due_date}}</div>
            </div>

            <table class="details-table">
                <tr>
                    <td>Admission Number</td>
                    <td>{{admission_number}}</td>
                </tr>
                <tr>
                    <td>Course</td>
                    <td>{{course_name}}</td>
                </tr>
                <tr>
                    <td>Amount Due</td>
                    <td>₦{{amount_due}}</td>
                </tr>
                <tr>
                    <td>Outstanding Balance</td>
                    <td>₦{{balance}}</td>
                </tr>
                <tr>
                    <td>Payment Deadline</td>
                    <td><strong style="color: #c2410c;">Today — {{due_date}}</strong></td>
                </tr>
            </table>

            <div class="consequences-list">
                <h3>Consequences of Non-Payment</h3>
                <ul>
                    <li>Your portal access will be automatically suspended tonight</li>
                    <li>You will lose access to all course materials, videos, and resources</li>
                    <li>Your cohort progress may be affected</li>
                    <li>Certificate issuance will be withheld until the balance is cleared</li>
                </ul>
            </div>

            <div class="alert-box">
                <p><strong>Grace Period:</strong> Payments received after 11:59 PM today will be treated as overdue. If you require a payment extension, please contact us <strong>before</strong> the deadline.</p>
            </div>

            <div class="cta-section">
                <a href="#" class="cta-button">Pay Now — ₦{{amount_due}}</a>
            </div>

            <p>If you are unable to make this payment today or need to discuss a payment plan, please contact our finance team immediately at <a href="mailto:finance@mapelead.com">finance@mapelead.com</a>.</p>
            <p>Thank you for your prompt attention to this matter.</p>
            <p>Regards,<br><strong>Mapelead Finance Team</strong></p>
        </div>
        <div class="email-footer">
            <p><strong>Mapelead Academy</strong> — Empowering Africa's Digital Future</p>
            <p><a href="#">support@mapelead.com</a> | <a href="#">www.mapelead.com</a></p>
            <p>© 2026 Mapelead. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
HTML,
        ];
    }
}
