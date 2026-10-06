export const API = {
  // Auth
  AUTH_LOGIN:              '/auth/login',
  AUTH_REGISTER:           '/auth/register',
  AUTH_LOGOUT:             '/auth/logout',
  AUTH_ME:                 '/me',
  AUTH_RESEND_VERIFY:      '/auth/resend-verification',
  AUTH_CHANGE_PASSWORD:    '/auth/change-password',
  AUTH_FORGOT_PASSWORD:    '/auth/forgot-password',
  AUTH_RESET_PASSWORD:     '/auth/reset-password',

  // Profile
  PROFILE:                 '/profile',
  PROFILE_AVATAR:          '/profile/avatar',
  PROFILE_STATS:           '/profile/stats',

  // Courses
  COURSES:                 '/courses',
  COURSES_FEATURED:        '/courses/featured',
  COURSES_SEARCH:          '/courses/search',
  COURSE_DETAIL:           (slug: string) => `/courses/${slug}`,
  CATEGORIES:              '/categories',

  // Enrollments
  MY_COURSES:              '/my-courses',
  ENROLLMENT:              (id: number) => `/my-courses/${id}`,
  ENROLL:                  (courseId: number) => `/enroll/${courseId}`,
  PAYMENT_VERIFY:          '/payment/verify',
  PAYMENT_HISTORY:         '/payment/history',

  // Lessons
  LESSON:                  (id: number) => `/lessons/${id}`,
  LESSON_COMPLETE:         (id: number) => `/lessons/${id}/complete`,
  LESSON_PROGRESS:         (id: number) => `/lessons/${id}/progress`,
  LESSON_BOOKMARK:         (id: number) => `/lessons/${id}/bookmark`,
  LESSON_NOTE:             (id: number) => `/lessons/${id}/note`,
  BOOKMARKS:               '/bookmarks',
  NOTES:                   '/notes',

  // Quizzes
  QUIZ:                    (id: number) => `/quizzes/${id}`,
  QUIZ_SUBMIT:             (id: number) => `/quizzes/${id}/submit`,
  QUIZ_ATTEMPTS:           (id: number) => `/quizzes/${id}/attempts`,

  // Assignments
  COURSE_ASSIGNMENTS:      (courseId: number) => `/courses/${courseId}/assignments`,
  ASSIGNMENT:              (id: number) => `/assignments/${id}`,
  ASSIGNMENT_SUBMIT:       (id: number) => `/assignments/${id}/submit`,

  // Certificates
  CERTIFICATES:            '/certificates',
  CERTIFICATE:             (id: number) => `/certificates/${id}`,
  CERTIFICATE_VERIFY:      (token: string) => `/certificates/verify/${token}`,
  CERTIFICATE_ELIGIBILITY: (courseId: number) => `/courses/${courseId}/certificate-eligibility`,

  // Notifications
  NOTIFICATIONS:           '/notifications',
  NOTIFICATIONS_UNREAD:    '/notifications/unread-count',
  NOTIFICATION_READ:       (id: number) => `/notifications/${id}/read`,
  NOTIFICATIONS_READ_ALL:  '/notifications/read-all',

  // Progress & Streaks
  COURSE_PROGRESS:         (courseId: number) => `/courses/${courseId}/progress`,
  STREAK:                  '/streak',

  // Instructor
  INSTRUCTOR_DASHBOARD:    '/instructor/dashboard',
  INSTRUCTOR_COURSES:      '/instructor/courses',
  INSTRUCTOR_SUBMISSIONS:  '/instructor/submissions',
  INSTRUCTOR_SUBMISSION:   (id: number) => `/instructor/submissions/${id}`,
  INSTRUCTOR_GRADE:        (id: number) => `/instructor/submissions/${id}/grade`,

  // Admin
  ADMIN_INSTRUCTOR_CODES:  '/admin/instructor-codes',

  // 2FA
  TWO_FA_SETUP:    '/auth/2fa/setup',
  TWO_FA_ENABLE:   '/auth/2fa/enable',
  TWO_FA_DISABLE:  '/auth/2fa/disable',

  // Reviews
  COURSE_REVIEWS:     (courseId: number) => `/courses/${courseId}/reviews`,
  COURSE_MY_REVIEW:   (courseId: number) => `/courses/${courseId}/my-review`,

  // Lesson navigation
  LESSON_NAVIGATION:  (id: number) => `/lessons/${id}/navigation`,

  // Certificate claim
  CERTIFICATE_CLAIM:  (courseId: number) => `/courses/${courseId}/certificate/claim`,

  // Support Tickets
  SUPPORT_TICKETS:       '/tickets',
  SUPPORT_TICKET:        (id: number) => `/tickets/${id}`,
  SUPPORT_TICKET_REPLY:  (id: number) => `/tickets/${id}/reply`,
  SUPPORT_TICKET_CLOSE:  (id: number) => `/tickets/${id}/close`,

  // Device token (push notifications)
  DEVICE_TOKEN:          '/device-token',

  // App settings (public — logo, name, tagline)
  APP_SETTINGS:          '/app-settings',
} as const;
