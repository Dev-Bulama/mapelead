export const API = {
  // Auth
  AUTH_LOGIN:              '/auth/login',
  AUTH_REGISTER:           '/auth/register',
  AUTH_LOGOUT:             '/auth/logout',
  AUTH_ME:                 '/me',
  AUTH_RESEND_VERIFY:      '/auth/resend-verification',
  AUTH_CHANGE_PASSWORD:    '/auth/change-password',

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

  // Admin
  ADMIN_INSTRUCTOR_CODES:  '/admin/instructor-codes',
} as const;
