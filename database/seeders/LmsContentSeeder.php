<?php

namespace Database\Seeders;

use App\Models\Batch;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseModule;
use App\Models\Instructor;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LmsContentSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Resolve instructor ───────────────────────────────────────────────────
        $instructorUser = User::role('instructor')->first()
            ?? User::role('admin')->first();

        if (! $instructorUser) {
            $this->command->warn('No instructor or admin user found. Skipping LmsContentSeeder.');
            return;
        }

        // Ensure the user has an Instructor profile row
        $instructor = Instructor::firstOrCreate(
            ['user_id' => $instructorUser->id],
            [
                'title'      => 'Senior Instructor',
                'expertise'  => 'Cybersecurity, Technology Training',
                'is_verified' => true,
                'is_featured' => true,
            ]
        );

        // ─── Course categories ────────────────────────────────────────────────────
        $categoryData = [
            [
                'name'        => 'Cybersecurity',
                'description' => 'Learn ethical hacking, penetration testing, network defense, and all things information security.',
                'icon'        => 'shield-check',
                'is_featured' => true,
            ],
            [
                'name'        => 'Data Science',
                'description' => 'Master data analysis, machine learning, Python, and turning raw data into actionable insights.',
                'icon'        => 'chart-bar',
                'is_featured' => true,
            ],
            [
                'name'        => 'Cloud Computing',
                'description' => 'Gain hands-on expertise in AWS, Azure, and Google Cloud — from practitioner to solutions architect.',
                'icon'        => 'cloud',
                'is_featured' => true,
            ],
            [
                'name'        => 'Software Development',
                'description' => 'Build modern web and mobile applications using industry-standard frameworks and best practices.',
                'icon'        => 'code-bracket',
                'is_featured' => true,
            ],
            [
                'name'        => 'Digital Marketing',
                'description' => 'Drive growth with SEO, social media strategy, content marketing, and the latest AI-powered marketing tools.',
                'icon'        => 'megaphone',
                'is_featured' => false,
            ],
        ];

        $categories = [];
        foreach ($categoryData as $idx => $cat) {
            $category = CourseCategory::firstOrCreate(
                ['name' => $cat['name']],
                array_merge($cat, [
                    'is_active'  => true,
                    'sort_order' => $idx + 1,
                ])
            );
            $categories[$cat['name']] = $category;
        }

        // ─── Course definitions ───────────────────────────────────────────────────
        $courseDefs = [
            [
                'title'             => 'Certified Ethical Hacking & Penetration Testing',
                'category'          => 'Cybersecurity',
                'price'             => 150000.00,
                'level'             => 'intermediate',
                'duration_hours'    => 120,
                'duration_weeks'    => 12,
                'short_description' => 'Master ethical hacking methodologies, penetration testing techniques, and security assessment tools used by professional security consultants worldwide.',
                'description'       => '<p>This comprehensive course covers everything you need to become a certified ethical hacker. You will learn to think like an attacker, identify vulnerabilities before malicious hackers do, and secure systems against real-world threats.</p><p>From reconnaissance and scanning to exploitation and post-exploitation, you will work through the full penetration testing lifecycle using industry-standard tools like Metasploit, Burp Suite, Nmap, and Wireshark.</p>',
                'what_you_learn'    => [
                    'Master the full penetration testing methodology',
                    'Use professional hacking tools (Metasploit, Burp Suite, Nmap)',
                    'Perform web application, network, and wireless security assessments',
                    'Write professional penetration testing reports',
                    'Prepare for CEH and OSCP certifications',
                ],
                'requirements'      => [
                    'Basic understanding of networking concepts (TCP/IP)',
                    'Familiarity with Linux command line',
                    'A laptop with at least 8GB RAM for virtual labs',
                ],
                'who_is_this_for'   => [
                    'IT professionals transitioning to cybersecurity',
                    'Network administrators who want to harden their systems',
                    'Students pursuing a career in information security',
                ],
                'modules'           => [
                    [
                        'title'       => 'Introduction to Ethical Hacking',
                        'description' => 'Foundational concepts, legal frameworks, and the hacker mindset.',
                        'lessons'     => [
                            ['title' => 'What is Ethical Hacking? Scope & Legal Boundaries', 'duration' => 20, 'is_preview' => true],
                            ['title' => 'Setting Up Your Hacking Lab with Kali Linux', 'duration' => 35],
                            ['title' => 'Networking Fundamentals for Hackers', 'duration' => 30],
                            ['title' => 'Reconnaissance Techniques: Passive & Active', 'duration' => 25],
                        ],
                    ],
                    [
                        'title'       => 'Scanning, Enumeration & Exploitation',
                        'description' => 'Deep dive into vulnerability scanning, service enumeration, and exploitation frameworks.',
                        'lessons'     => [
                            ['title' => 'Port Scanning with Nmap — Advanced Techniques', 'duration' => 40],
                            ['title' => 'Vulnerability Scanning with OpenVAS & Nessus', 'duration' => 35],
                            ['title' => 'Exploitation with Metasploit Framework', 'duration' => 45],
                            ['title' => 'Password Attacks: Cracking & Spraying', 'duration' => 30],
                        ],
                    ],
                    [
                        'title'       => 'Advanced Attacks & Reporting',
                        'description' => 'Web application hacking, post-exploitation, and professional report writing.',
                        'lessons'     => [
                            ['title' => 'Web Application Penetration Testing with Burp Suite', 'duration' => 45],
                            ['title' => 'SQL Injection, XSS & OWASP Top 10 Exploits', 'duration' => 40],
                            ['title' => 'Post-Exploitation: Pivoting & Persistence', 'duration' => 35],
                            ['title' => 'Writing Professional Penetration Testing Reports', 'duration' => 25],
                        ],
                    ],
                ],
            ],
            [
                'title'             => 'Data Science with Python & Machine Learning',
                'category'          => 'Data Science',
                'price'             => 120000.00,
                'level'             => 'intermediate',
                'duration_hours'    => 100,
                'duration_weeks'    => 10,
                'short_description' => 'Go from raw data to production-ready machine learning models. Learn Python, pandas, scikit-learn, and real-world data storytelling.',
                'description'       => '<p>This hands-on data science bootcamp takes you from Python fundamentals to building and deploying machine learning models. You will work on real Nigerian datasets and business problems throughout the course.</p><p>By the end, you will be able to clean and analyse data, build predictive models, create compelling visualisations, and communicate insights to non-technical stakeholders.</p>',
                'what_you_learn'    => [
                    'Python programming for data analysis',
                    'Data wrangling with pandas and NumPy',
                    'Data visualisation with Matplotlib and Seaborn',
                    'Build and evaluate machine learning models with scikit-learn',
                    'Work with real-world datasets and business use cases',
                ],
                'requirements'      => [
                    'No prior programming experience required',
                    'Basic mathematics (algebra and statistics helpful)',
                    'A computer with internet access',
                ],
                'who_is_this_for'   => [
                    'Business analysts who want to add data science skills',
                    'Graduates seeking careers in data and analytics',
                    'Professionals looking to make data-driven decisions',
                ],
                'modules'           => [
                    [
                        'title'       => 'Python for Data Science Foundations',
                        'description' => 'Core Python programming skills needed for data analysis and machine learning.',
                        'lessons'     => [
                            ['title' => 'Python Crash Course: Variables, Data Types & Control Flow', 'duration' => 30, 'is_preview' => true],
                            ['title' => 'Functions, List Comprehensions & File Handling', 'duration' => 25],
                            ['title' => 'NumPy: Fast Numerical Computing', 'duration' => 35],
                            ['title' => 'Pandas: DataFrames, Indexing & Data Cleaning', 'duration' => 40],
                        ],
                    ],
                    [
                        'title'       => 'Data Analysis & Visualisation',
                        'description' => 'Explore, analyse, and visualise data to uncover meaningful patterns and insights.',
                        'lessons'     => [
                            ['title' => 'Exploratory Data Analysis (EDA) Workflow', 'duration' => 35],
                            ['title' => 'Data Visualisation with Matplotlib & Seaborn', 'duration' => 30],
                            ['title' => 'Statistical Analysis: Distributions, Hypothesis Testing', 'duration' => 40],
                            ['title' => 'Storytelling with Data: Building Dashboards', 'duration' => 25],
                        ],
                    ],
                    [
                        'title'       => 'Machine Learning & Capstone Project',
                        'description' => 'Build, train, and evaluate machine learning models on real datasets.',
                        'lessons'     => [
                            ['title' => 'Supervised Learning: Regression & Classification', 'duration' => 45],
                            ['title' => 'Model Evaluation, Cross-Validation & Hyperparameter Tuning', 'duration' => 35],
                            ['title' => 'Unsupervised Learning: Clustering & Dimensionality Reduction', 'duration' => 30],
                            ['title' => 'Capstone: End-to-End ML Project from Kaggle Dataset', 'duration' => 45],
                        ],
                    ],
                ],
            ],
            [
                'title'             => 'AWS Cloud Practitioner + Solutions Architect',
                'category'          => 'Cloud Computing',
                'price'             => 95000.00,
                'level'             => 'beginner',
                'duration_hours'    => 80,
                'duration_weeks'    => 8,
                'short_description' => 'Gain industry-recognised AWS certifications. Learn core cloud services, architecture best practices, and prepare for both the CCP and SAA-C03 exams.',
                'description'       => '<p>This dual-certification prep course covers AWS Cloud Practitioner and AWS Solutions Architect — Associate. You will get hands-on lab experience in a real AWS environment, building the confidence to design scalable, reliable, and cost-effective cloud architectures.</p>',
                'what_you_learn'    => [
                    'Core AWS services: EC2, S3, RDS, Lambda, VPC',
                    'Cloud security and IAM best practices',
                    'Designing fault-tolerant and highly available architectures',
                    'Cost optimisation strategies on AWS',
                    'Exam tips and practice tests for CCP and SAA-C03',
                ],
                'requirements'      => [
                    'Basic IT literacy (no cloud experience needed)',
                    'A free AWS account (we show you how to set one up)',
                ],
                'who_is_this_for'   => [
                    'IT professionals wanting to move into cloud roles',
                    'Developers who want to understand cloud infrastructure',
                    'Anyone preparing for AWS certification exams',
                ],
                'modules'           => [
                    [
                        'title'       => 'Cloud Fundamentals & AWS Core Services',
                        'description' => 'Introduction to cloud computing and the most important AWS services.',
                        'lessons'     => [
                            ['title' => 'What is Cloud Computing? AWS Global Infrastructure', 'duration' => 20, 'is_preview' => true],
                            ['title' => 'IAM: Users, Groups, Roles & Policies', 'duration' => 30],
                            ['title' => 'EC2: Launching & Managing Virtual Servers', 'duration' => 40],
                            ['title' => 'S3: Object Storage, Versioning & Lifecycle Policies', 'duration' => 30],
                        ],
                    ],
                    [
                        'title'       => 'Networking, Databases & Security',
                        'description' => 'VPC design, database services, and AWS security best practices.',
                        'lessons'     => [
                            ['title' => 'VPC: Subnets, Route Tables, Security Groups & NACLs', 'duration' => 45],
                            ['title' => 'RDS, DynamoDB & ElastiCache Overview', 'duration' => 35],
                            ['title' => 'AWS Security Services: CloudTrail, GuardDuty, WAF', 'duration' => 30],
                            ['title' => 'Monitoring with CloudWatch & AWS Trusted Advisor', 'duration' => 25],
                        ],
                    ],
                    [
                        'title'       => 'Architecture Best Practices & Exam Preparation',
                        'description' => 'Well-Architected Framework, serverless, and exam strategies.',
                        'lessons'     => [
                            ['title' => 'Serverless with Lambda, API Gateway & SQS', 'duration' => 40],
                            ['title' => 'High Availability: Auto Scaling & Load Balancing', 'duration' => 35],
                            ['title' => 'AWS Well-Architected Framework — 6 Pillars', 'duration' => 30],
                            ['title' => 'CCP & SAA-C03 Exam Tips, Practice Tests & Review', 'duration' => 45],
                        ],
                    ],
                ],
            ],
            [
                'title'             => 'Full Stack Web Development (React + Laravel)',
                'category'          => 'Software Development',
                'price'             => 130000.00,
                'level'             => 'intermediate',
                'duration_hours'    => 140,
                'duration_weeks'    => 16,
                'short_description' => 'Build production-ready web applications from front-end to back-end. Master React.js, Laravel, REST APIs, and deploy to the cloud.',
                'description'       => '<p>This immersive full-stack bootcamp teaches you to build complete web applications using two of the most in-demand frameworks: React for the front-end and Laravel for the back-end API.</p><p>You will build multiple real-world projects, including an e-commerce platform and a task management SaaS app, graduating with a portfolio that impresses employers.</p>',
                'what_you_learn'    => [
                    'Build responsive UIs with React, Tailwind CSS & TypeScript',
                    'Develop RESTful APIs with Laravel 11',
                    'Implement JWT authentication and role-based access control',
                    'Work with MySQL, Redis, and file storage',
                    'Deploy applications to DigitalOcean / AWS',
                ],
                'requirements'      => [
                    'Basic HTML, CSS and JavaScript knowledge',
                    'Understanding of web concepts (HTTP, browsers)',
                    'A code editor (VS Code recommended)',
                ],
                'who_is_this_for'   => [
                    'Front-end developers who want to become full-stack',
                    'Back-end developers wanting to learn React',
                    'Graduates building their first professional portfolio',
                ],
                'modules'           => [
                    [
                        'title'       => 'Front-End Mastery with React',
                        'description' => 'Modern React development with hooks, state management, and API integration.',
                        'lessons'     => [
                            ['title' => 'React Fundamentals: JSX, Components & Props', 'duration' => 30, 'is_preview' => true],
                            ['title' => 'State Management with useState, useEffect & Context API', 'duration' => 40],
                            ['title' => 'React Router, Protected Routes & Layouts', 'duration' => 30],
                            ['title' => 'Tailwind CSS: Building Beautiful Responsive UIs', 'duration' => 35],
                        ],
                    ],
                    [
                        'title'       => 'Back-End API Development with Laravel',
                        'description' => 'RESTful API development, authentication, queues, and database design.',
                        'lessons'     => [
                            ['title' => 'Laravel 11 Architecture: Routes, Controllers & Middleware', 'duration' => 35],
                            ['title' => 'Eloquent ORM: Relationships, Scopes & Mutators', 'duration' => 40],
                            ['title' => 'API Authentication with Laravel Sanctum & JWT', 'duration' => 35],
                            ['title' => 'Queues, Events, Notifications & Laravel Horizon', 'duration' => 30],
                        ],
                    ],
                    [
                        'title'       => 'Full Stack Integration & Deployment',
                        'description' => 'Connect React to Laravel APIs, testing, and production deployment.',
                        'lessons'     => [
                            ['title' => 'Connecting React to Laravel API with Axios & React Query', 'duration' => 40],
                            ['title' => 'Testing: PHPUnit for Laravel, Vitest for React', 'duration' => 35],
                            ['title' => 'CI/CD Pipelines with GitHub Actions', 'duration' => 30],
                            ['title' => 'Deploying to DigitalOcean: Nginx, SSL & Domain Setup', 'duration' => 40],
                        ],
                    ],
                ],
            ],
            [
                'title'             => 'Cybersecurity Fundamentals for Beginners',
                'category'          => 'Cybersecurity',
                'price'             => 45000.00,
                'level'             => 'beginner',
                'duration_hours'    => 40,
                'duration_weeks'    => 4,
                'short_description' => 'The perfect starting point for anyone who wants to understand cybersecurity. Learn how attacks happen and how to defend against them — no prior experience needed.',
                'description'       => '<p>This beginner-friendly course demystifies cybersecurity. You will learn how hackers think, understand common attack vectors like phishing, malware, and social engineering, and discover practical steps to protect yourself and your organisation online.</p>',
                'what_you_learn'    => [
                    'Core cybersecurity concepts: CIA Triad, risk, and threats',
                    'How common cyberattacks work (phishing, ransomware, MITM)',
                    'Network security basics: firewalls, VPNs, and encryption',
                    'Password security, MFA, and safe browsing habits',
                    'Introduction to security frameworks: NIST, ISO 27001',
                ],
                'requirements'      => [
                    'No technical background required',
                    'Basic computer literacy',
                    'A willingness to learn and ask questions',
                ],
                'who_is_this_for'   => [
                    'Complete beginners curious about cybersecurity',
                    'Business owners wanting to protect their companies',
                    'Office workers handling sensitive data',
                ],
                'modules'           => [
                    [
                        'title'       => 'Introduction to Cybersecurity',
                        'description' => 'What is cybersecurity, why it matters, and the threat landscape in Nigeria and globally.',
                        'lessons'     => [
                            ['title' => 'The Cybersecurity Landscape: Threats, Actors & Motives', 'duration' => 15, 'is_preview' => true],
                            ['title' => 'CIA Triad: Confidentiality, Integrity & Availability', 'duration' => 20],
                            ['title' => 'Common Cyber Threats: Malware, Phishing & Ransomware', 'duration' => 25],
                            ['title' => 'Social Engineering: How Hackers Exploit Human Psychology', 'duration' => 20],
                        ],
                    ],
                    [
                        'title'       => 'Network & System Security Basics',
                        'description' => 'Understanding network security controls and how to secure systems.',
                        'lessons'     => [
                            ['title' => 'How Networks Work: IP, DNS, HTTP & HTTPS', 'duration' => 25],
                            ['title' => 'Firewalls, Intrusion Detection & VPNs', 'duration' => 20],
                            ['title' => 'Securing Your Devices: OS Hardening & Patch Management', 'duration' => 20],
                            ['title' => 'Encryption: How Data is Protected in Transit & at Rest', 'duration' => 25],
                        ],
                    ],
                    [
                        'title'       => 'Personal & Organisational Security',
                        'description' => 'Practical security habits and compliance frameworks for organisations.',
                        'lessons'     => [
                            ['title' => 'Password Management, MFA & Identity Security', 'duration' => 20],
                            ['title' => 'Safe Browsing, Email Security & Remote Work Safety', 'duration' => 15],
                            ['title' => 'Incident Response: What to Do When You Are Hacked', 'duration' => 20],
                            ['title' => 'Security Frameworks: NIST, ISO 27001 & GDPR Basics', 'duration' => 25],
                        ],
                    ],
                ],
            ],
            [
                'title'             => 'Digital Marketing Mastery with AI Tools',
                'category'          => 'Digital Marketing',
                'price'             => 65000.00,
                'level'             => 'beginner',
                'duration_hours'    => 60,
                'duration_weeks'    => 6,
                'short_description' => 'Learn modern digital marketing from SEO and social media to AI-powered content creation and paid advertising strategies that drive real business results.',
                'description'       => '<p>This practical digital marketing course equips you with the skills to grow any business online. You will learn to create compelling content, run profitable ad campaigns, optimise for search engines, and leverage AI tools like ChatGPT and Canva AI to produce more in less time.</p>',
                'what_you_learn'    => [
                    'SEO: rank on Google and drive organic traffic',
                    'Social media marketing across Instagram, LinkedIn & TikTok',
                    'Run Meta and Google Ads campaigns profitably',
                    'Email marketing automation and list building',
                    'Use AI tools (ChatGPT, Canva AI, Jasper) to 10x your output',
                ],
                'requirements'      => [
                    'No prior marketing experience required',
                    'A smartphone or computer with internet access',
                    'Willingness to implement what you learn immediately',
                ],
                'who_is_this_for'   => [
                    'Entrepreneurs wanting to market their own businesses',
                    'Job seekers building a digital marketing career',
                    'Content creators wanting to monetise their audience',
                ],
                'modules'           => [
                    [
                        'title'       => 'Digital Marketing Foundations & Content Strategy',
                        'description' => 'Understanding the digital marketing landscape and building a winning content strategy.',
                        'lessons'     => [
                            ['title' => 'Digital Marketing in 2026: Channels, Funnels & Metrics', 'duration' => 20, 'is_preview' => true],
                            ['title' => 'Defining Your Target Audience & Buyer Persona', 'duration' => 25],
                            ['title' => 'Content Marketing Strategy: Planning & Editorial Calendar', 'duration' => 30],
                            ['title' => 'Using AI (ChatGPT & Canva AI) to Create Content Fast', 'duration' => 35],
                        ],
                    ],
                    [
                        'title'       => 'SEO, Social Media & Email Marketing',
                        'description' => 'Organic growth strategies to build a loyal audience and rank on search engines.',
                        'lessons'     => [
                            ['title' => 'SEO Fundamentals: Keyword Research, On-Page & Technical SEO', 'duration' => 40],
                            ['title' => 'Social Media Marketing: Instagram, LinkedIn & TikTok', 'duration' => 35],
                            ['title' => 'Email Marketing: List Building, Sequences & Automation', 'duration' => 30],
                            ['title' => 'Analytics: Measuring What Matters with Google Analytics 4', 'duration' => 25],
                        ],
                    ],
                    [
                        'title'       => 'Paid Advertising & Growth Hacking',
                        'description' => 'Run profitable Meta and Google Ads and implement growth marketing frameworks.',
                        'lessons'     => [
                            ['title' => 'Meta Ads: Campaign Structure, Targeting & Creative', 'duration' => 40],
                            ['title' => 'Google Ads: Search, Display & YouTube Campaigns', 'duration' => 35],
                            ['title' => 'Conversion Rate Optimisation & Landing Page Design', 'duration' => 30],
                            ['title' => 'Building a Digital Marketing Portfolio & Getting Clients', 'duration' => 25],
                        ],
                    ],
                ],
            ],
        ];

        // ─── Create courses, modules, and lessons ─────────────────────────────────
        $createdCourses = [];
        $firstLessonCreated = false; // tracks global first lesson across all courses

        foreach ($courseDefs as $courseIndex => $courseDef) {
            $category = $categories[$courseDef['category']];

            $course = Course::firstOrCreate(
                ['title' => $courseDef['title']],
                [
                    'instructor_id'       => $instructor->id,
                    'category_id'         => $category->id,
                    'price'               => $courseDef['price'],
                    'currency'            => 'NGN',
                    'level'               => $courseDef['level'],
                    'status'              => 'published',
                    'is_published'        => true,
                    'published_at'        => now(),
                    'is_featured'         => $courseIndex < 3,
                    'certificate_enabled' => true,
                    'is_free'             => false,
                    'language'            => 'English',
                    'duration_hours'      => $courseDef['duration_hours'],
                    'duration_weeks'      => $courseDef['duration_weeks'],
                    'short_description'   => $courseDef['short_description'],
                    'description'         => $courseDef['description'],
                    'requirements'        => $courseDef['requirements'],
                    'what_you_learn'      => $courseDef['what_you_learn'],
                    'who_is_this_for'     => $courseDef['who_is_this_for'],
                    'total_lessons'       => 12,
                ]
            );

            $createdCourses[] = $course;

            // Create AttendanceSettings for this course
            DB::table('attendance_settings')->updateOrInsert(
                ['course_id' => $course->id],
                [
                    'minimum_percentage'            => 75,
                    'notify_threshold'              => 80,
                    'notify_student_below_threshold' => true,
                    'notify_admin_below_threshold'   => true,
                    'block_certificate_below_minimum' => true,
                    'created_at'                    => now(),
                    'updated_at'                    => now(),
                ]
            );

            // Create modules and lessons
            foreach ($courseDef['modules'] as $moduleIndex => $moduleDef) {
                $module = CourseModule::firstOrCreate(
                    [
                        'course_id' => $course->id,
                        'title'     => $moduleDef['title'],
                    ],
                    [
                        'description'    => $moduleDef['description'],
                        'sort_order'     => $moduleIndex + 1,
                        'is_free_preview' => false,
                    ]
                );

                foreach ($moduleDef['lessons'] as $lessonIndex => $lessonDef) {
                    // Only the very first lesson of the very first course is a free preview
                    $isPreview = isset($lessonDef['is_preview']) && $lessonDef['is_preview'] && ! $firstLessonCreated;
                    if ($isPreview) {
                        $firstLessonCreated = true;
                    }

                    Lesson::firstOrCreate(
                        [
                            'module_id' => $module->id,
                            'title'     => $lessonDef['title'],
                        ],
                        [
                            'course_id'      => $course->id,
                            'type'           => 'video',
                            'duration_minutes' => $lessonDef['duration'],
                            'sort_order'     => $lessonIndex + 1,
                            'is_published'   => true,
                            'is_free_preview' => $isPreview,
                            'content'        => '<p>This lesson covers: <strong>' . $lessonDef['title'] . '</strong>. '
                                . 'Watch the video above and complete the exercises at the end of the lesson. '
                                . 'Use the resources tab to download the accompanying materials.</p>',
                        ]
                    );
                }
            }
        }

        // ─── Batches for the first two courses ───────────────────────────────────
        if (count($createdCourses) >= 1) {
            Batch::firstOrCreate(
                ['code' => 'CEH-COHORT-7'],
                [
                    'course_id'        => $createdCourses[0]->id,
                    'name'             => 'Cohort 7 — Jan 2026',
                    'description'      => 'January 2026 intake for Certified Ethical Hacking & Penetration Testing.',
                    'start_date'       => '2026-01-10',
                    'end_date'         => null,
                    'max_students'     => 30,
                    'current_students' => 0,
                    'status'           => 'active',
                ]
            );
        }

        if (count($createdCourses) >= 2) {
            Batch::firstOrCreate(
                ['code' => 'DS-COHORT-3'],
                [
                    'course_id'        => $createdCourses[1]->id,
                    'name'             => 'Cohort 3 — Feb 2026',
                    'description'      => 'February 2026 intake for Data Science with Python & Machine Learning.',
                    'start_date'       => '2026-02-01',
                    'end_date'         => null,
                    'max_students'     => 25,
                    'current_students' => 0,
                    'status'           => 'upcoming',
                ]
            );
        }

        $this->command->info('LmsContentSeeder: created ' . count($createdCourses) . ' courses with modules, lessons, attendance settings, and batches.');
    }
}
