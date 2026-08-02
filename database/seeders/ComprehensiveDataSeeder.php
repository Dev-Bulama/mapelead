<?php

namespace Database\Seeders;

use App\Models\Batch;
use App\Models\BatchEnrollment;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseModule;
use App\Models\Enrollment;
use App\Models\Instructor;
use App\Models\Lesson;
use App\Models\Payment;
use App\Models\StudentProgress;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ComprehensiveDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedBlogCategories();
        $this->seedBlogPosts();
        $this->seedAdditionalStudents();
        $this->seedFullCourseWithEnrollments();

        $this->command->info('ComprehensiveDataSeeder complete.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    private function seedBlogCategories(): void
    {
        $cats = [
            ['name' => 'Cybersecurity News',  'color' => '#EF4444', 'sort_order' => 1],
            ['name' => 'Tech Careers',         'color' => '#3B82F6', 'sort_order' => 2],
            ['name' => 'Learning Tips',        'color' => '#10B981', 'sort_order' => 3],
            ['name' => 'Industry Insights',    'color' => '#8B5CF6', 'sort_order' => 4],
            ['name' => 'Student Success',      'color' => '#F59E0B', 'sort_order' => 5],
        ];
        foreach ($cats as $c) {
            BlogCategory::firstOrCreate(['name' => $c['name']], array_merge($c, ['is_active' => true]));
        }
    }

    private function seedBlogPosts(): void
    {
        $adminUser = User::whereHas('roles', fn($q) => $q->where('name', 'admin'))->first();
        if (! $adminUser) return;

        $catMap = BlogCategory::pluck('id', 'name');

        $posts = [
            [
                'title'       => 'Why Cybersecurity Is the Most In-Demand Skill of 2026',
                'category'    => 'Cybersecurity News',
                'excerpt'     => 'The global cybersecurity talent gap has reached 3.5 million unfilled positions. Here is why now is the perfect time to start your cybersecurity career.',
                'content'     => '<h2>The Cybersecurity Skills Gap</h2><p>The global cybersecurity workforce gap has grown to an unprecedented 3.5 million unfilled positions worldwide, according to the latest (ISC)² Cybersecurity Workforce Study. In Nigeria alone, the demand for skilled cybersecurity professionals has grown by over 200% in the past three years.</p><h2>What This Means for You</h2><p>This talent shortage translates into exceptional career opportunities for those who choose to enter the field. Entry-level cybersecurity analysts in Nigeria now command starting salaries of ₦600,000–₦1,200,000 per year, with senior professionals earning ₦3,000,000 and above.</p><h2>Key Roles in High Demand</h2><ul><li>Penetration Testers / Ethical Hackers</li><li>Security Operations Center (SOC) Analysts</li><li>Cloud Security Engineers</li><li>Incident Response Specialists</li><li>Compliance and Risk Analysts</li></ul><p>At MapeLeads, our Certified Ethical Hacking course is designed to prepare you for these roles in just 12 weeks. With hands-on lab exercises and real-world scenarios, you will graduate job-ready.</p>',
                'status'      => 'published',
                'is_featured' => true,
            ],
            [
                'title'       => '5 Study Habits Every Online Learner Should Adopt',
                'category'    => 'Learning Tips',
                'excerpt'     => 'Mastering online learning takes more than just watching videos. These five habits will help you retain more and progress faster.',
                'content'     => '<h2>1. The Pomodoro Technique</h2><p>Study in focused 25-minute blocks with 5-minute breaks. This technique has been shown to improve focus and retention by up to 40% compared to marathon study sessions.</p><h2>2. Active Recall Over Passive Review</h2><p>Instead of re-reading notes, test yourself regularly. Use flashcards, quiz yourself, or explain concepts aloud as if teaching someone else. This is the single most effective study technique according to cognitive science research.</p><h2>3. Build a Dedicated Learning Space</h2><p>Your environment shapes your mindset. Create a distraction-free zone specifically for studying — this signals to your brain that it is time to focus.</p><h2>4. Engage With the Community</h2><p>Join discussion forums, ask questions in live sessions, and connect with fellow students. Learning is social, and peer interaction deepens understanding.</p><h2>5. Apply What You Learn Immediately</h2><p>Every lesson should end with a practical exercise. In our cybersecurity courses, students immediately practice each technique in our virtual labs. Hands-on application is the fastest path from knowledge to skill.</p>',
                'status'      => 'published',
                'is_featured' => false,
            ],
            [
                'title'       => 'From Student to Senior Developer: Amaka\'s Story',
                'category'    => 'Student Success',
                'excerpt'     => 'How one MapeLeads graduate went from zero coding experience to landing a senior developer role at a fintech company in 18 months.',
                'content'     => '<h2>Starting From Zero</h2><p>When Amaka Okonkwo enrolled in the Software Development bootcamp at MapeLeads, she had no technical background. A former marketing executive, she had decided to pivot her career after seeing her company struggle to hire qualified developers.</p><blockquote>"I was terrified. Everyone else seemed to know so much already. But the instructors made sure no one was left behind."</blockquote><h2>The Journey</h2><p>Amaka completed the full-stack web development track over 12 weeks, choosing the Physical Intensive format to stay accountable. She attended three sessions per day, Monday through Friday, and completed over 200 hours of hands-on projects.</p><h2>The Outcome</h2><p>Within 3 months of graduating, Amaka had built three portfolio projects and landed her first role as a junior developer. 15 months later, she was promoted to senior developer. Today she mentors new MapeLeads students.</p><p>"MapeLeads did not just teach me to code. It taught me how to learn, how to problem-solve, and how to keep going when things get hard. That mindset is what got me promoted."</p>',
                'status'      => 'published',
                'is_featured' => true,
            ],
            [
                'title'       => 'Cloud Computing Certifications: Which One Should You Get First?',
                'category'    => 'Tech Careers',
                'excerpt'     => 'AWS, Azure, or Google Cloud? We break down the top cloud certifications and help you decide which path makes sense for your career goals.',
                'content'     => '<h2>The Big Three: AWS, Azure, GCP</h2><p>Cloud computing has become the backbone of modern technology infrastructure. Virtually every business of any size now relies on cloud services, making cloud skills among the most transferable in the tech industry.</p><h2>AWS Certified Cloud Practitioner</h2><p>The ideal entry point for most people. Amazon Web Services holds roughly 33% of the global cloud market share, making AWS skills the most universally demanded. The Cloud Practitioner exam is foundational and typically takes 2–3 months to prepare for.</p><h2>Microsoft Azure Fundamentals (AZ-900)</h2><p>If you are targeting corporate environments, especially those already in the Microsoft ecosystem (Office 365, Teams, SharePoint), Azure skills are often a better fit. Microsoft holds about 22% of the cloud market.</p><h2>Google Cloud Associate Cloud Engineer</h2><p>For data science and machine learning workloads, Google Cloud is often preferred. GCP holds about 11% market share but is growing rapidly.</p><h2>Our Recommendation</h2><p>Start with AWS if you want maximum job flexibility. Our 12-week Cloud Computing course covers AWS with deep dives into compute, storage, networking, and serverless architectures — preparing you for the AWS Solutions Architect Associate exam.</p>',
                'status'      => 'published',
                'is_featured' => false,
            ],
        ];

        foreach ($posts as $p) {
            $categoryId = $catMap[$p['category']] ?? null;
            $slug = Str::slug($p['title']);
            $existing = BlogPost::where('slug', $slug)->first();
            if ($existing) continue;

            BlogPost::create([
                'user_id'            => $adminUser->id,
                'category_id'        => $categoryId,
                'title'              => $p['title'],
                'excerpt'            => $p['excerpt'],
                'content'            => $p['content'],
                'status'             => $p['status'],
                'is_featured'        => $p['is_featured'],
                'allow_comments'     => true,
                'views'              => rand(45, 890),
                'read_time_minutes'  => max(1, (int)(str_word_count(strip_tags($p['content'])) / 200)),
                'published_at'       => now()->subDays(rand(1, 60)),
            ]);
        }
    }

    private function seedAdditionalStudents(): void
    {
        $students = [
            ['first_name' => 'Ibrahim',   'last_name' => 'Musa',     'email' => 'ibrahim.musa@student.com'],
            ['first_name' => 'Fatima',    'last_name' => 'Aliyu',    'email' => 'fatima.aliyu@student.com'],
            ['first_name' => 'Emeka',     'last_name' => 'Nwosu',    'email' => 'emeka.nwosu@student.com'],
            ['first_name' => 'Blessing',  'last_name' => 'Eze',      'email' => 'blessing.eze@student.com'],
            ['first_name' => 'Yakubu',    'last_name' => 'Hassan',   'email' => 'yakubu.hassan@student.com'],
            ['first_name' => 'Ngozi',     'last_name' => 'Obi',      'email' => 'ngozi.obi@student.com'],
            ['first_name' => 'Kabiru',    'last_name' => 'Lawal',    'email' => 'kabiru.lawal@student.com'],
            ['first_name' => 'Chidinma',  'last_name' => 'Okeke',    'email' => 'chidinma.okeke@student.com'],
        ];

        foreach ($students as $s) {
            $user = User::firstOrCreate(
                ['email' => $s['email']],
                [
                    'first_name'        => $s['first_name'],
                    'last_name'         => $s['last_name'],
                    'password'          => Hash::make('Student@1234'),
                    'status'            => 'active',
                    'email_verified_at' => now(),
                    'country'           => 'Nigeria',
                    'admission_number'  => 'MPL-' . now()->year . '-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT),
                ]
            );
            $user->syncRoles(['student']);
        }
    }

    private function seedFullCourseWithEnrollments(): void
    {
        // ── Resolve instructor ─────────────────────────────────────────────────
        $instructorUser = User::whereHas('roles', fn($q) => $q->whereIn('name', ['instructor', 'admin']))->first();
        if (! $instructorUser) return;

        $instructor = Instructor::firstOrCreate(
            ['user_id' => $instructorUser->id],
            ['title' => 'Senior Instructor', 'expertise' => 'Cybersecurity, Technology', 'is_verified' => true]
        );

        // ── Course category ────────────────────────────────────────────────────
        $category = CourseCategory::firstOrCreate(
            ['name' => 'Cybersecurity'],
            ['description' => 'Ethical hacking, penetration testing, and information security.', 'is_active' => true, 'sort_order' => 1]
        );

        // ── The fully-built course ─────────────────────────────────────────────
        $course = Course::firstOrCreate(
            ['slug' => 'network-security-fundamentals'],
            [
                'instructor_id'     => $instructor->id,
                'category_id'       => $category->id,
                'title'             => 'Network Security Fundamentals',
                'short_description' => 'Build a solid foundation in network security — firewalls, VPNs, IDS/IPS, network forensics, and secure architecture design.',
                'description'       => '<p>This course takes you from the core concepts of TCP/IP networking through to advanced security controls used in enterprise environments. You will configure firewalls, set up VPNs, analyse network traffic with Wireshark, and understand how to detect and respond to intrusions.</p><p>Ideal for IT professionals seeking to specialise in security, or security enthusiasts who want a structured, practical pathway into network defence.</p>',
                'type'              => 'hybrid',
                'level'             => 'beginner',
                'status'            => 'published',
                'is_published'      => true,
                'published_at'      => now()->subMonths(3),
                'duration_hours'    => 60,
                'duration_weeks'    => 8,
                'price'             => 80000.00,
                'discount_price'    => 65000.00,
                'currency'          => 'NGN',
                'is_free'           => false,
                'is_featured'       => true,
                'certificate_enabled' => true,
                'total_lessons'     => 24,
                'total_students'    => 0,
                'requirements'      => ['Basic understanding of computer networking', 'Familiarity with the Windows or Linux command line'],
                'what_you_learn'    => [
                    'Understand TCP/IP, OSI model, and network protocols',
                    'Configure and manage firewalls and ACLs',
                    'Set up and troubleshoot VPN connections',
                    'Analyse network traffic using Wireshark',
                    'Detect intrusions with IDS/IPS tools',
                    'Implement network segmentation and DMZ architectures',
                    'Perform basic network forensic investigations',
                ],
                'who_is_this_for'   => ['IT administrators who want to move into security', 'Networking professionals seeking a security specialisation', 'Security students building foundational skills'],
                'thumbnail'         => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600',
                'meta_title'        => 'Network Security Fundamentals Course — MapeLeads',
                'meta_description'  => 'Learn firewalls, VPNs, IDS/IPS and network forensics in 8 weeks.',
            ]
        );

        // ── Modules and lessons ────────────────────────────────────────────────
        $curriculum = [
            [
                'title'       => 'Networking Refresher',
                'description' => 'Solidify your understanding of TCP/IP, OSI model, and the protocols that underpin modern networks.',
                'lessons'     => [
                    ['title' => 'The OSI Model Explained', 'duration' => 18],
                    ['title' => 'TCP/IP Deep Dive', 'duration' => 25],
                    ['title' => 'IP Addressing and Subnetting', 'duration' => 30],
                    ['title' => 'Common Protocols: DNS, DHCP, HTTP, FTP', 'duration' => 22],
                ],
            ],
            [
                'title'       => 'Firewalls and Access Control',
                'description' => 'Learn how firewalls work, how to configure rules, and common firewall architectures.',
                'lessons'     => [
                    ['title' => 'Firewall Types: Packet Filter, Stateful, NGFW', 'duration' => 20],
                    ['title' => 'Configuring pfSense Firewall Rules', 'duration' => 35],
                    ['title' => 'Access Control Lists (ACLs)', 'duration' => 25],
                    ['title' => 'DMZ and Network Segmentation', 'duration' => 28],
                ],
            ],
            [
                'title'       => 'VPN Technologies',
                'description' => 'Understand VPN protocols and set up secure tunnels for remote access and site-to-site connectivity.',
                'lessons'     => [
                    ['title' => 'VPN Concepts and Use Cases', 'duration' => 15],
                    ['title' => 'IPSec vs SSL/TLS VPNs', 'duration' => 22],
                    ['title' => 'OpenVPN Lab: Server Setup', 'duration' => 40],
                    ['title' => 'WireGuard: Modern VPN Setup', 'duration' => 35],
                ],
            ],
            [
                'title'       => 'Traffic Analysis with Wireshark',
                'description' => 'Capture and analyse network packets to identify anomalies, understand protocols in action, and detect attacks.',
                'lessons'     => [
                    ['title' => 'Installing and Navigating Wireshark', 'duration' => 15],
                    ['title' => 'Capturing and Filtering Packets', 'duration' => 25],
                    ['title' => 'Identifying Suspicious Traffic Patterns', 'duration' => 30],
                    ['title' => 'Analysing a Real Attack PCAP File', 'duration' => 40],
                ],
            ],
            [
                'title'       => 'Intrusion Detection and Prevention',
                'description' => 'Deploy and tune IDS/IPS solutions to detect and block malicious activity in real time.',
                'lessons'     => [
                    ['title' => 'IDS vs IPS: Concepts and Deployment', 'duration' => 18],
                    ['title' => 'Setting Up Snort IDS', 'duration' => 35],
                    ['title' => 'Writing Custom Snort Rules', 'duration' => 30],
                    ['title' => 'Suricata and NIDS/HIDS Comparison', 'duration' => 25],
                ],
            ],
            [
                'title'       => 'Network Forensics',
                'description' => 'Learn how to investigate network incidents by collecting, preserving, and analysing digital evidence.',
                'lessons'     => [
                    ['title' => 'Incident Response Fundamentals', 'duration' => 20],
                    ['title' => 'Network Log Analysis', 'duration' => 30],
                    ['title' => 'Evidence Collection and Chain of Custody', 'duration' => 22],
                    ['title' => 'Capstone: Full Network Forensics Lab', 'duration' => 60],
                ],
            ],
        ];

        $lessonCount = 0;
        foreach ($curriculum as $mIdx => $moduleDef) {
            $module = CourseModule::firstOrCreate(
                ['course_id' => $course->id, 'title' => $moduleDef['title']],
                [
                    'description'     => $moduleDef['description'],
                    'sort_order'      => $mIdx + 1,
                    'is_free_preview' => $mIdx === 0,
                ]
            );

            foreach ($moduleDef['lessons'] as $lIdx => $lessonDef) {
                $isPreview = $mIdx === 0 && $lIdx === 0;
                Lesson::firstOrCreate(
                    ['module_id' => $module->id, 'title' => $lessonDef['title']],
                    [
                        'course_id'        => $course->id,
                        'type'             => 'video',
                        'duration_minutes' => $lessonDef['duration'],
                        'sort_order'       => $lIdx + 1,
                        'is_published'     => true,
                        'is_free_preview'  => $isPreview,
                        'content'          => '<p>This lesson covers <strong>' . $lessonDef['title'] . '</strong>.</p><p>Watch the video above and follow along in your lab environment. Lab files are available in the Resources tab.</p>',
                    ]
                );
                $lessonCount++;
            }
        }

        // Update lesson count
        $course->update(['total_lessons' => $lessonCount]);

        // ── Batch ──────────────────────────────────────────────────────────────
        $batch = Batch::firstOrCreate(
            ['code' => 'NSF-COHORT-2'],
            [
                'course_id'        => $course->id,
                'name'             => 'Cohort 2 — May 2026',
                'description'      => 'May 2026 intake for Network Security Fundamentals.',
                'start_date'       => now()->subWeeks(2),
                'end_date'         => now()->addWeeks(6),
                'max_students'     => 20,
                'current_students' => 0,
                'status'           => 'active',
            ]
        );

        // ── Enroll sample students ─────────────────────────────────────────────
        $studentEmails = [
            'ibrahim.musa@student.com',
            'fatima.aliyu@student.com',
            'emeka.nwosu@student.com',
        ];

        $allLessons = Lesson::where('course_id', $course->id)->where('is_published', true)->get();

        foreach ($studentEmails as $idx => $email) {
            $student = User::where('email', $email)->first();
            if (! $student) continue;

            $existing = Enrollment::where('user_id', $student->id)->where('course_id', $course->id)->first();
            if ($existing) continue;

            $trainingTypes = ['online', 'physical_monthly', 'physical_quarterly'];

            $enrollment = Enrollment::create([
                'user_id'        => $student->id,
                'course_id'      => $course->id,
                'status'         => 'active',
                'payment_status' => 'paid',
                'payment_type'   => 'full',
                'training_type'  => $trainingTypes[$idx] ?? 'online',
                'amount_paid'    => $course->effective_price,
                'currency'       => 'NGN',
                'enrolled_at'    => now()->subWeeks(2),
                'progress_percent' => 0,
            ]);

            // Batch enrollment
            BatchEnrollment::firstOrCreate([
                'batch_id'      => $batch->id,
                'enrollment_id' => $enrollment->id,
            ]);

            // Record payment
            Payment::create([
                'user_id'       => $student->id,
                'enrollment_id' => $enrollment->id,
                'reference'     => 'MPL-' . strtoupper(Str::random(10)),
                'gateway'       => 'paystack',
                'amount'        => $course->effective_price,
                'currency'      => 'NGN',
                'status'        => 'success',
                'payment_method'=> 'card',
                'paid_at'       => now()->subWeeks(2),
            ]);

            // Mark progress: first two students have partial progress
            if ($idx === 0) {
                // First student: 100% complete — will get a certificate
                foreach ($allLessons as $lesson) {
                    StudentProgress::firstOrCreate([
                        'user_id'   => $student->id,
                        'lesson_id' => $lesson->id,
                    ], [
                        'course_id'          => $course->id,
                        'is_completed'       => true,
                        'completed_at'       => now()->subDays(rand(1, 10)),
                        'watch_time_seconds' => $lesson->duration_minutes * 60,
                    ]);
                }

                $enrollment->update([
                    'progress_percent' => 100,
                    'status'           => 'completed',
                    'completed_at'     => now()->subDays(3),
                ]);

                Certificate::firstOrCreate(
                    ['enrollment_id' => $enrollment->id],
                    [
                        'user_id'          => $student->id,
                        'course_id'        => $course->id,
                        'certificate_number'=> 'CERT-NSF-' . str_pad($student->id, 5, '0', STR_PAD_LEFT),
                        'issued_at'        => now()->subDays(3),
                        'expires_at'       => null,
                        'status'           => 'issued',
                    ]
                );
            } elseif ($idx === 1) {
                // Second student: 50% progress
                $halfLessons = $allLessons->take((int)($allLessons->count() / 2));
                foreach ($halfLessons as $lesson) {
                    StudentProgress::firstOrCreate([
                        'user_id'   => $student->id,
                        'lesson_id' => $lesson->id,
                    ], [
                        'course_id'          => $course->id,
                        'is_completed'       => true,
                        'completed_at'       => now()->subDays(rand(3, 8)),
                        'watch_time_seconds' => $lesson->duration_minutes * 60,
                    ]);
                }
                $enrollment->update(['progress_percent' => 50]);
            }
            // Third student: 0% (just enrolled)
        }

        // ── One student with installment payment ───────────────────────────────
        $installmentStudent = User::where('email', 'blessing.eze@student.com')->first();
        if ($installmentStudent && ! Enrollment::where('user_id', $installmentStudent->id)->where('course_id', $course->id)->exists()) {
            $enrollment2 = Enrollment::create([
                'user_id'        => $installmentStudent->id,
                'course_id'      => $course->id,
                'status'         => 'active',
                'payment_status' => 'partial',
                'payment_type'   => 'installment',
                'training_type'  => 'online',
                'amount_paid'    => 25000.00,
                'currency'       => 'NGN',
                'enrolled_at'    => now()->subWeeks(1),
                'progress_percent' => 0,
            ]);

            Payment::create([
                'user_id'       => $installmentStudent->id,
                'enrollment_id' => $enrollment2->id,
                'reference'     => 'MPL-' . strtoupper(Str::random(10)),
                'gateway'       => 'paystack',
                'amount'        => 25000.00,
                'currency'      => 'NGN',
                'status'        => 'success',
                'payment_method'=> 'bank_transfer',
                'notes'         => 'Installment 1 of 3',
                'paid_at'       => now()->subWeeks(1),
            ]);

            // Create installment plan
            if (class_exists(\App\Models\InstallmentPlan::class)) {
                $plan = \App\Models\InstallmentPlan::firstOrCreate(
                    ['enrollment_id' => $enrollment2->id],
                    [
                        'user_id'            => $installmentStudent->id,
                        'course_id'          => $course->id,
                        'total_amount'       => $course->effective_price,
                        'down_payment'       => 25000.00,
                        'amount_paid'        => 25000.00,
                        'installment_count'  => 3,
                        'outstanding_balance'=> $course->effective_price - 25000.00,
                        'status'             => 'active',
                        'grace_period_days'  => 3,
                    ]
                );
            }
        }

        // Update course student count
        $count = Enrollment::where('course_id', $course->id)->whereIn('status', ['active', 'completed'])->count();
        $course->update(['total_students' => $count]);
        $batch->update(['current_students' => BatchEnrollment::where('batch_id', $batch->id)->count()]);

        $this->command->info('  ✓ Network Security Fundamentals course: ' . $lessonCount . ' lessons, ' . $count . ' enrolled students');
    }
}
