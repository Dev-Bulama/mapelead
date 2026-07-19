@extends('layouts.admin')
@section('title', 'Documentation')

@section('content')
<div class="max-w-5xl mx-auto space-y-8" x-data="{ active: 'overview' }">

    {{-- Page header --}}
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Platform Documentation</h2>
        <p class="text-sm text-gray-500 mt-0.5">Complete guide to managing the MapeLearn platform.</p>
    </div>

    <div class="flex flex-col lg:flex-row gap-6">

        {{-- Sidebar navigation --}}
        <nav class="lg:w-56 shrink-0">
            <div class="bg-white rounded-xl border border-gray-200 p-3 sticky top-4 space-y-0.5">
                @php
                $sections = [
                    'overview'     => ['icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', 'label' => 'Overview'],
                    'courses'      => ['icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', 'label' => 'Courses & Modules'],
                    'enrollments'  => ['icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z', 'label' => 'Enrollments'],
                    'payments'     => ['icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z', 'label' => 'Payments'],
                    'installments' => ['icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'label' => 'Installments'],
                    'users'        => ['icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z', 'label' => 'Users & Roles'],
                    'cms'          => ['icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z', 'label' => 'CMS & Content'],
                    'settings'     => ['icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z', 'label' => 'Settings'],
                    'integrations' => ['icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'label' => 'Integrations'],
                    'production'   => ['icon' => 'M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01', 'label' => 'Production Checklist'],
                ];
                @endphp

                @foreach($sections as $key => $sec)
                <button @click="active = '{{ $key }}'"
                        :class="active === '{{ $key }}' ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-gray-600 hover:bg-gray-50'"
                        class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm transition-colors text-left">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sec['icon'] }}"/>
                    </svg>
                    {{ $sec['label'] }}
                </button>
                @endforeach
            </div>
        </nav>

        {{-- Content --}}
        <div class="flex-1 min-w-0 space-y-6">

            {{-- ══════════════════════════════ OVERVIEW ══════════════════════════════ --}}
            <div x-show="active === 'overview'" x-cloak class="space-y-4">
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Platform Overview</h3>
                    <p class="text-sm text-gray-600 leading-relaxed mb-4">
                        MapeLearn is a full-featured Learning Management System (LMS) built on Laravel. It supports course creation, student enrollment, payment processing (Paystack), installment plans, progress tracking, certificates, quizzes, assignments, and a CMS for managing all public-facing content.
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach([
                            ['label' => 'Admin Panel', 'desc' => 'Full control over courses, users, payments, CMS, and settings.'],
                            ['label' => 'Instructor Portal', 'desc' => 'Instructors manage their own courses, modules, and lessons.'],
                            ['label' => 'Student Dashboard', 'desc' => 'Students access enrolled courses, track progress, download certificates.'],
                            ['label' => 'Payment Gateway', 'desc' => 'Paystack integration with full-payment and installment options.'],
                        ] as $item)
                        <div class="bg-gray-50 rounded-xl p-4">
                            <p class="font-semibold text-sm text-gray-900">{{ $item['label'] }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $item['desc'] }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-brand-50 border border-brand-200 rounded-xl p-5">
                    <h4 class="font-semibold text-brand-900 mb-2">Quick Links</h4>
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        @foreach([
                            ['route' => 'admin.courses.index',    'label' => 'Manage Courses'],
                            ['route' => 'admin.enrollments.index','label' => 'Enrollments'],
                            ['route' => 'admin.payments.index',   'label' => 'Payments'],
                            ['route' => 'admin.users.index',      'label' => 'Users'],
                            ['route' => 'admin.cms.index',        'label' => 'CMS Builder'],
                            ['route' => 'admin.settings.index',   'label' => 'Settings'],
                        ] as $link)
                        <a href="{{ route($link['route']) }}" class="flex items-center gap-1.5 text-brand-700 hover:text-brand-900 font-medium">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            {{ $link['label'] }}
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ══════════════════════════════ COURSES ══════════════════════════════ --}}
            <div x-show="active === 'courses'" x-cloak class="space-y-4">
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Courses & Modules</h3>

                    <div class="space-y-5">
                        <div>
                            <h4 class="font-semibold text-gray-800 mb-2">Creating a Course</h4>
                            <ol class="space-y-1.5 text-sm text-gray-600 list-decimal list-inside">
                                <li>Go to <strong>Courses → Add Course</strong>.</li>
                                <li>Fill in the course title, slug, category, and description.</li>
                                <li>Upload a thumbnail image (JPG/PNG, recommended 800×450 px).</li>
                                <li>Set the course price or mark it as free.</li>
                                <li>Set <strong>Training Mode Prices</strong> — Online, Physical 1-Month, Physical 3-Month. Leave blank to use the base price for all modes.</li>
                                <li>Configure <strong>Installment Plans</strong> — define the number of payments and interval in days. Students will choose from these options at checkout.</li>
                                <li>Optionally upload a course brochure (PDF).</li>
                                <li>Save as Draft. Publish when ready.</li>
                            </ol>
                        </div>

                        <div class="border-t border-gray-100 pt-5">
                            <h4 class="font-semibold text-gray-800 mb-2">Modules & Lessons</h4>
                            <ol class="space-y-1.5 text-sm text-gray-600 list-decimal list-inside">
                                <li>Open a course and click <strong>Add Module</strong>.</li>
                                <li>Inside each module, add Lessons. Each lesson supports Video URL, rich text content, and attachments.</li>
                                <li>Student progress is tracked per lesson. Lessons can be marked complete via the learn page.</li>
                                <li>Reorder modules and lessons by adjusting sort order.</li>
                            </ol>
                        </div>

                        <div class="border-t border-gray-100 pt-5">
                            <h4 class="font-semibold text-gray-800 mb-2">Publishing</h4>
                            <p class="text-sm text-gray-600">A course must be explicitly <strong>published</strong> before it appears on the public course catalogue. Use the <em>Publish / Unpublish</em> toggle on the course list or detail page. Draft courses are invisible to students.</p>
                        </div>

                        <div class="border-t border-gray-100 pt-5">
                            <h4 class="font-semibold text-gray-800 mb-2">Batches</h4>
                            <p class="text-sm text-gray-600">Batches let you group enrollments into cohorts with a start and end date. Create batches under <strong>Batches</strong> and assign students to them during or after enrollment. Attendance can be tracked per batch.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ══════════════════════════════ ENROLLMENTS ══════════════════════════ --}}
            <div x-show="active === 'enrollments'" x-cloak class="space-y-4">
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Enrollments</h3>
                    <div class="space-y-5">
                        <div>
                            <h4 class="font-semibold text-gray-800 mb-2">Enrollment Statuses</h4>
                            <div class="space-y-2 text-sm text-gray-600">
                                @foreach([
                                    ['status' => 'pending',   'color' => 'yellow', 'desc' => 'Enrollment created but payment not yet confirmed.'],
                                    ['status' => 'active',    'color' => 'green',  'desc' => 'Student has paid and has full access to the course.'],
                                    ['status' => 'suspended', 'color' => 'red',    'desc' => 'Admin has manually suspended the student\'s access.'],
                                    ['status' => 'completed', 'color' => 'blue',   'desc' => 'Student has finished the course (100% progress).'],
                                ] as $s)
                                <div class="flex items-start gap-3">
                                    <span class="inline-block mt-0.5 px-2 py-0.5 text-xs font-semibold rounded-full
                                        {{ $s['color'] === 'yellow' ? 'bg-yellow-100 text-yellow-700' : ($s['color'] === 'green' ? 'bg-green-100 text-green-700' : ($s['color'] === 'red' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700')) }}">
                                        {{ ucfirst($s['status']) }}
                                    </span>
                                    <span>{{ $s['desc'] }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="border-t border-gray-100 pt-5">
                            <h4 class="font-semibold text-gray-800 mb-2">Payment Status</h4>
                            <ul class="space-y-1.5 text-sm text-gray-600 list-disc list-inside">
                                <li><strong>unpaid</strong> — No payment received yet.</li>
                                <li><strong>partial</strong> — Down payment received (installment plan active).</li>
                                <li><strong>paid</strong> — Full amount paid.</li>
                            </ul>
                        </div>
                        <div class="border-t border-gray-100 pt-5">
                            <h4 class="font-semibold text-gray-800 mb-2">Access Locking</h4>
                            <p class="text-sm text-gray-600">When an installment payment becomes overdue (past due date + grace period), the system automatically locks the student's course access. The student sees a notification and must pay to regain access. Admins can manually unlock via <em>Enrollments → Unlock Access</em>.</p>
                        </div>
                        <div class="border-t border-gray-100 pt-5">
                            <h4 class="font-semibold text-gray-800 mb-2">Admission Numbers</h4>
                            <p class="text-sm text-gray-600">When an enrollment is approved, an admission number is automatically generated based on your configured format (Settings → Admission Numbers). Students can download their admission slip from their dashboard.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ══════════════════════════════ PAYMENTS ══════════════════════════════ --}}
            <div x-show="active === 'payments'" x-cloak class="space-y-4">
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Payments</h3>
                    <div class="space-y-5">
                        <div>
                            <h4 class="font-semibold text-gray-800 mb-2">How Paystack Works</h4>
                            <ol class="space-y-1.5 text-sm text-gray-600 list-decimal list-inside">
                                <li>Student selects a course and chooses a training mode (Online / Physical 1-Month / Physical 3-Month).</li>
                                <li>If paying in full, they are redirected to Paystack's secure payment page.</li>
                                <li>After payment, Paystack redirects to the callback URL and the system verifies the transaction.</li>
                                <li>The enrollment status updates to <em>active</em> and the payment is recorded.</li>
                            </ol>
                        </div>
                        <div class="border-t border-gray-100 pt-5">
                            <h4 class="font-semibold text-gray-800 mb-2">Coupons</h4>
                            <p class="text-sm text-gray-600 mb-2">Go to <strong>Coupons</strong> in the admin sidebar to create discount codes. Each coupon has:</p>
                            <ul class="space-y-1 text-sm text-gray-600 list-disc list-inside">
                                <li>A unique code (e.g. SAVE20)</li>
                                <li>Discount type: fixed amount or percentage</li>
                                <li>Optional expiry date and maximum usage limit</li>
                                <li>Optional course restriction (apply only to specific courses)</li>
                            </ul>
                        </div>
                        <div class="border-t border-gray-100 pt-5">
                            <h4 class="font-semibold text-gray-800 mb-2">Refunds</h4>
                            <p class="text-sm text-gray-600">Admin can initiate a refund from <strong>Payments → View → Refund</strong>. Refunds are processed through Paystack's API. Ensure the original payment is still within Paystack's refund window.</p>
                        </div>
                        <div class="border-t border-gray-100 pt-5">
                            <h4 class="font-semibold text-gray-800 mb-2">Webhooks</h4>
                            <p class="text-sm text-gray-600">Paystack sends webhook events to <code class="bg-gray-100 px-1 rounded text-xs">/webhooks/paystack</code>. Configure this URL in your Paystack dashboard under Settings → API Keys & Webhooks. The webhook secret must match the one set in Admin → Settings → Integrations.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ══════════════════════════════ INSTALLMENTS ═════════════════════════ --}}
            <div x-show="active === 'installments'" x-cloak class="space-y-4">
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Installment Plans</h3>
                    <div class="space-y-5">
                        <div>
                            <h4 class="font-semibold text-gray-800 mb-2">Setting Up Installment Options (Admin)</h4>
                            <ol class="space-y-1.5 text-sm text-gray-600 list-decimal list-inside">
                                <li>Edit the course and scroll to <strong>Installment Plans</strong>.</li>
                                <li>Add payment spread options — each option defines the number of payments and the interval in days (max 30 days between payments).</li>
                                <li>Add a label such as "2 Payments, every 14 days".</li>
                                <li>If no options are configured, four defaults are shown: 2×14d, 3×10d, 4×7d, and 2×21d.</li>
                            </ol>
                        </div>
                        <div class="border-t border-gray-100 pt-5">
                            <h4 class="font-semibold text-gray-800 mb-2">Student Checkout Flow</h4>
                            <ol class="space-y-1.5 text-sm text-gray-600 list-decimal list-inside">
                                <li>Student selects "Installment" payment type.</li>
                                <li>A list of available spread options appears. Student picks one.</li>
                                <li>Student enters the down payment amount and the date for the first installment.</li>
                                <li>Down payment is charged immediately via Paystack.</li>
                                <li>Remaining installments are tracked by the system. Student pays each via their dashboard.</li>
                            </ol>
                        </div>
                        <div class="border-t border-gray-100 pt-5">
                            <h4 class="font-semibold text-gray-800 mb-2">Overdue Processing</h4>
                            <p class="text-sm text-gray-600">The system checks for overdue installments. When a payment is past its due date (+ grace period), the student's access is locked automatically. A notification is sent. The admin can also manually unlock access from <strong>Enrollments → Unlock</strong>.</p>
                        </div>
                        <div class="border-t border-gray-100 pt-5">
                            <h4 class="font-semibold text-gray-800 mb-2">Admin View</h4>
                            <p class="text-sm text-gray-600">View all installment plans under <strong>Payments → Installments</strong>. You can record a manual payment for a student from that screen.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ══════════════════════════════ USERS ════════════════════════════════ --}}
            <div x-show="active === 'users'" x-cloak class="space-y-4">
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Users & Roles</h3>
                    <div class="space-y-5">
                        <div>
                            <h4 class="font-semibold text-gray-800 mb-2">Roles</h4>
                            <div class="space-y-2 text-sm text-gray-600">
                                @foreach([
                                    ['role' => 'super_admin', 'desc' => 'Full access to everything including system settings and user role changes.'],
                                    ['role' => 'admin',       'desc' => 'Full admin panel access. Cannot change super_admin accounts.'],
                                    ['role' => 'instructor',  'desc' => 'Access to instructor portal. Can manage their own courses and students.'],
                                    ['role' => 'student',     'desc' => 'Default role. Access to student dashboard, enrolled courses, certificates.'],
                                ] as $r)
                                <div class="flex items-start gap-3">
                                    <span class="inline-block mt-0.5 px-2 py-0.5 text-xs font-mono font-semibold bg-gray-100 text-gray-700 rounded">{{ $r['role'] }}</span>
                                    <span>{{ $r['desc'] }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="border-t border-gray-100 pt-5">
                            <h4 class="font-semibold text-gray-800 mb-2">User Statuses</h4>
                            <ul class="space-y-1.5 text-sm text-gray-600 list-disc list-inside">
                                <li><strong>active</strong> — Can log in and use the platform.</li>
                                <li><strong>pending</strong> — Registered but awaiting admin approval or email verification.</li>
                                <li><strong>inactive</strong> — Deactivated. Cannot log in.</li>
                                <li><strong>suspended</strong> — Blocked from accessing the platform.</li>
                            </ul>
                        </div>
                        <div class="border-t border-gray-100 pt-5">
                            <h4 class="font-semibold text-gray-800 mb-2">Forgot Password</h4>
                            <p class="text-sm text-gray-600">The forgot password flow works via email. To enable it, configure SMTP settings in <strong>Settings → Integrations → Email / SMTP</strong>. Set the mail driver to <em>smtp</em> and provide your host, port, username, and password. Once configured, users can request a reset link from the login page.</p>
                        </div>
                        <div class="border-t border-gray-100 pt-5">
                            <h4 class="font-semibold text-gray-800 mb-2">Exporting Users</h4>
                            <p class="text-sm text-gray-600">Use the <em>Export</em> button on the Users list to download a CSV of all users. Contact form submissions and newsletter subscribers also have export buttons on their respective pages.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ══════════════════════════════ CMS ══════════════════════════════════ --}}
            <div x-show="active === 'cms'" x-cloak class="space-y-4">
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">CMS & Content Management</h3>
                    <div class="space-y-5">
                        <div>
                            <h4 class="font-semibold text-gray-800 mb-2">Hero Banners</h4>
                            <p class="text-sm text-gray-600 mb-2">Manage the rotating slides on the homepage hero section under <strong>CMS Builder → Hero Banners</strong>.</p>
                            <ul class="space-y-1 text-sm text-gray-600 list-disc list-inside">
                                <li>Add a new banner with title, subtitle, description, badge text, two CTA buttons, and an image.</li>
                                <li>To edit an existing banner, click the <em>Edit</em> button — a full form appears with all fields including image replacement.</li>
                                <li>Toggle active/inactive to control visibility. Only active banners appear on the site.</li>
                                <li>Use sort order to control the sequence of slides.</li>
                                <li>Recommended image size: 1200×600 px or wider (landscape).</li>
                            </ul>
                        </div>
                        <div class="border-t border-gray-100 pt-5">
                            <h4 class="font-semibold text-gray-800 mb-2">Services</h4>
                            <p class="text-sm text-gray-600 mb-2">Manage the services grid on the homepage under <strong>CMS → Services</strong>.</p>
                            <ul class="space-y-1 text-sm text-gray-600 list-disc list-inside">
                                <li>Each service has a title, description, icon (SVG keyword or emoji), color, optional image, and a CTA link.</li>
                                <li>Click <em>Edit</em> to modify all fields including changing the icon and uploading a new image.</li>
                                <li>Featured services appear prominently. Active services are visible to the public.</li>
                            </ul>
                        </div>
                        <div class="border-t border-gray-100 pt-5">
                            <h4 class="font-semibold text-gray-800 mb-2">Team Members</h4>
                            <p class="text-sm text-gray-600 mb-2">Add team members under <strong>Team</strong>. Each member has a name, position, department, bio, photo, and optional LinkedIn/Twitter links. On the about page, team cards are displayed in a centered flex grid — partial rows are automatically centered.</p>
                        </div>
                        <div class="border-t border-gray-100 pt-5">
                            <h4 class="font-semibold text-gray-800 mb-2">Blog</h4>
                            <p class="text-sm text-gray-600">Create blog posts under <strong>Blog → Posts</strong>. Posts support categories, tags, featured images, and rich content. Publish when ready to make them visible. Published posts appear at <code class="bg-gray-100 px-1 rounded text-xs">/blog</code>.</p>
                        </div>
                        <div class="border-t border-gray-100 pt-5">
                            <h4 class="font-semibold text-gray-800 mb-2">Pages</h4>
                            <p class="text-sm text-gray-600">Create static pages with custom slugs using the Page Builder. Pages support rich HTML content and meta tags for SEO. Useful for Terms of Service, Privacy Policy, or any custom landing page.</p>
                        </div>
                        <div class="border-t border-gray-100 pt-5">
                            <h4 class="font-semibold text-gray-800 mb-2">Contact Form Submissions</h4>
                            <p class="text-sm text-gray-600">View and reply to contact form submissions under <strong>Leads & CRM → Contact Submissions</strong>. Admin can reply to each message via email from within the panel.</p>
                        </div>
                        <div class="border-t border-gray-100 pt-5">
                            <h4 class="font-semibold text-gray-800 mb-2">Testimonials & FAQs</h4>
                            <p class="text-sm text-gray-600">Manage testimonials and FAQs via their dedicated sections. Student-submitted reviews appear as inactive and require admin approval before showing on the site.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ══════════════════════════════ SETTINGS ═════════════════════════════ --}}
            <div x-show="active === 'settings'" x-cloak class="space-y-4">
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Settings</h3>
                    <div class="space-y-5">
                        @foreach([
                            ['tab' => 'General',   'desc' => 'Site name, email, phone, address, logo, and favicon. These values are used throughout the site and email communications.'],
                            ['tab' => 'Homepage',  'desc' => 'Headline text, subtitle, hero image, and promotional content for the homepage sections.'],
                            ['tab' => 'Social',    'desc' => 'Links to your social media profiles (Facebook, Twitter/X, Instagram, LinkedIn, YouTube, WhatsApp). WhatsApp number adds a "Chat on WhatsApp" button to the contact page.'],
                            ['tab' => 'SEO',       'desc' => 'Global meta title, description, Open Graph image, and Google Analytics / Tag Manager IDs.'],
                            ['tab' => 'Footer',    'desc' => 'Footer tagline, copyright text, and additional footer links.'],
                            ['tab' => 'Integrations', 'desc' => 'Paystack API keys, SMTP/email settings, Google Sign-In credentials, reCAPTCHA keys, and contact form button text.'],
                        ] as $tab)
                        <div>
                            <h4 class="font-semibold text-gray-800 mb-1">{{ $tab['tab'] }}</h4>
                            <p class="text-sm text-gray-600">{{ $tab['desc'] }}</p>
                        </div>
                        @if(!$loop->last)<div class="border-t border-gray-100"></div>@endif
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ══════════════════════════════ INTEGRATIONS ═════════════════════════ --}}
            <div x-show="active === 'integrations'" x-cloak class="space-y-4">
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Integrations Setup Guide</h3>
                    <div class="space-y-6">

                        <div>
                            <h4 class="font-semibold text-gray-800 mb-2">1. Paystack Payment Gateway</h4>
                            <ol class="space-y-1.5 text-sm text-gray-600 list-decimal list-inside">
                                <li>Sign up at <strong>paystack.com</strong> and create a business account.</li>
                                <li>Go to <em>Settings → API Keys & Webhooks</em> in your Paystack dashboard.</li>
                                <li>Copy the <strong>Public Key</strong> and <strong>Secret Key</strong>.</li>
                                <li>Paste them into <strong>Admin → Settings → Integrations → Paystack Payments</strong>.</li>
                                <li>In Paystack, add the webhook URL: <code class="bg-gray-100 px-1 rounded text-xs">{{ url('/webhooks/paystack') }}</code></li>
                                <li>Copy the webhook secret from Paystack and paste it in the Webhook Secret field.</li>
                                <li>Start with <em>Test Mode</em> keys, then switch to <em>Live Mode</em> keys for production.</li>
                            </ol>
                        </div>

                        <div class="border-t border-gray-100 pt-5">
                            <h4 class="font-semibold text-gray-800 mb-2">2. Email / SMTP</h4>
                            <p class="text-sm text-gray-600 mb-2">Required for password reset emails and notification emails.</p>
                            <ol class="space-y-1.5 text-sm text-gray-600 list-decimal list-inside">
                                <li>Select mail driver: choose <strong>SMTP</strong> for production.</li>
                                <li>Popular SMTP providers: <em>Mailgun</em>, <em>SendGrid</em>, <em>Gmail</em>, or your hosting provider's SMTP.</li>
                                <li>Common settings — Host: <code class="bg-gray-100 px-1 rounded text-xs">smtp.gmail.com</code>, Port: <code class="bg-gray-100 px-1 rounded text-xs">587</code>, Encryption: <code class="bg-gray-100 px-1 rounded text-xs">tls</code>.</li>
                                <li>For Gmail: enable 2FA and generate an <em>App Password</em> to use as the SMTP password.</li>
                                <li>Set <em>From Address</em> to a verified sender email.</li>
                                <li>After saving, test by using the Forgot Password feature.</li>
                            </ol>
                        </div>

                        <div class="border-t border-gray-100 pt-5">
                            <h4 class="font-semibold text-gray-800 mb-2">3. Google Sign-In</h4>
                            <ol class="space-y-1.5 text-sm text-gray-600 list-decimal list-inside">
                                <li>Go to <a href="https://console.cloud.google.com" target="_blank" class="text-brand-600 hover:underline">Google Cloud Console</a>.</li>
                                <li>Create a project, enable the <em>Google+ API</em> or <em>People API</em>.</li>
                                <li>Under <em>Credentials</em>, create an OAuth 2.0 Client ID (Web Application).</li>
                                <li>Add the authorized redirect URI: <code class="bg-gray-100 px-1 rounded text-xs">{{ url('/auth/google/callback') }}</code></li>
                                <li>Copy the Client ID and Client Secret into the admin settings.</li>
                                <li>Enable the toggle to show the "Continue with Google" button.</li>
                            </ol>
                        </div>

                        <div class="border-t border-gray-100 pt-5">
                            <h4 class="font-semibold text-gray-800 mb-2">4. reCAPTCHA (Contact Form Protection)</h4>
                            <ol class="space-y-1.5 text-sm text-gray-600 list-decimal list-inside">
                                <li>Go to <a href="https://www.google.com/recaptcha/admin" target="_blank" class="text-brand-600 hover:underline">Google reCAPTCHA Admin</a>.</li>
                                <li>Register a new site — choose <strong>reCAPTCHA v2 "I'm not a robot"</strong>.</li>
                                <li>Add your domain(s) to the allowed domains list.</li>
                                <li>Copy the <strong>Site Key</strong> (public) and <strong>Secret Key</strong> (private).</li>
                                <li>Paste both into <strong>Settings → Integrations → Contact Form</strong>.</li>
                                <li>Enable the toggle. The captcha widget will now appear on the contact form.</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>

            {{-- ══════════════════════════════ PRODUCTION ═══════════════════════════ --}}
            <div x-show="active === 'production'" x-cloak class="space-y-4">
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Production Deployment Checklist</h3>
                    <p class="text-sm text-gray-500 mb-5">Run through these steps each time you deploy to the production server.</p>

                    <div class="space-y-3">
                        @foreach([
                            ['cmd' => 'php artisan migrate --force',    'desc' => 'Apply any pending database migrations.'],
                            ['cmd' => 'php artisan storage:link',        'desc' => 'Create the public/storage symlink so uploaded files are accessible.'],
                            ['cmd' => 'php artisan config:cache',        'desc' => 'Cache config for performance.'],
                            ['cmd' => 'php artisan route:cache',         'desc' => 'Cache routes for performance.'],
                            ['cmd' => 'php artisan view:cache',          'desc' => 'Pre-compile Blade views.'],
                            ['cmd' => 'php artisan optimize',            'desc' => 'Run all optimization commands at once.'],
                            ['cmd' => 'php artisan queue:restart',       'desc' => 'If using queues, restart workers to pick up new code.'],
                        ] as $step)
                        <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-xl">
                            <code class="text-xs font-mono bg-gray-800 text-green-400 px-2 py-1 rounded shrink-0 mt-0.5">{{ $step['cmd'] }}</code>
                            <p class="text-sm text-gray-600">{{ $step['desc'] }}</p>
                        </div>
                        @endforeach
                    </div>

                    <div class="mt-6 space-y-4">
                        <div class="border-t border-gray-100 pt-5">
                            <h4 class="font-semibold text-gray-800 mb-2">Image / File Uploads Not Showing</h4>
                            <p class="text-sm text-gray-600">If uploaded images are not rendering, the storage symlink is missing. Run <code class="bg-gray-100 px-1 rounded text-xs">php artisan storage:link</code> on the server. On cPanel, use the Terminal in cPanel or SSH to run this command from the project root.</p>
                        </div>
                        <div class="border-t border-gray-100 pt-5">
                            <h4 class="font-semibold text-gray-800 mb-2">.env File</h4>
                            <p class="text-sm text-gray-600">Ensure <code class="bg-gray-100 px-1 rounded text-xs">APP_ENV=production</code> and <code class="bg-gray-100 px-1 rounded text-xs">APP_DEBUG=false</code> are set in your production <code class="bg-gray-100 px-1 rounded text-xs">.env</code> file. Never expose debug mode to the public.</p>
                        </div>
                        <div class="border-t border-gray-100 pt-5">
                            <h4 class="font-semibold text-gray-800 mb-2">Scheduled Tasks (Cron)</h4>
                            <p class="text-sm text-gray-600 mb-2">Add a cron job on your server to run Laravel's scheduler every minute. On cPanel, add a cron job with this command:</p>
                            <code class="block bg-gray-800 text-green-400 text-xs font-mono px-4 py-3 rounded-xl">* * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1</code>
                            <p class="text-sm text-gray-500 mt-2">This handles installment overdue checks, announcement expirations, and other periodic tasks.</p>
                        </div>
                        <div class="border-t border-gray-100 pt-5">
                            <h4 class="font-semibold text-gray-800 mb-2">Clearing Cache After Settings Changes</h4>
                            <p class="text-sm text-gray-600">After updating site settings, run <code class="bg-gray-100 px-1 rounded text-xs">php artisan cache:clear</code> and <code class="bg-gray-100 px-1 rounded text-xs">php artisan config:cache</code> to apply changes immediately.</p>
                        </div>
                    </div>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-5">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-yellow-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div>
                            <p class="font-semibold text-yellow-800 text-sm">Security Reminder</p>
                            <ul class="mt-1 space-y-1 text-xs text-yellow-700 list-disc list-inside">
                                <li>Keep your <code>.env</code> file out of version control (<code>.gitignore</code>).</li>
                                <li>Use strong, unique passwords for all admin accounts.</li>
                                <li>Enable HTTPS (SSL certificate) in production.</li>
                                <li>Regularly back up the database and uploaded files.</li>
                                <li>Keep Laravel and package dependencies up to date.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
