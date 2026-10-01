// ── Auth ─────────────────────────────────────────────────────────────────────

export interface User {
  id: number;
  first_name: string;
  last_name: string;
  full_name: string;
  email: string;
  phone: string | null;
  avatar_url: string | null;
  email_verified: boolean;
  two_factor_enabled: boolean;
  roles: string[];
  admission_number: string | null;
  enrollments_count?: number;
  completed_courses?: number;
  created_at?: string;
}

export interface AuthTokens {
  token: string;
}

export interface LoginPayload {
  email: string;
  password: string;
  totp_code?: string;
  device_name?: string;
}

export interface RegisterPayload {
  first_name: string;
  last_name: string;
  email: string;
  phone?: string;
  password: string;
  password_confirmation: string;
  device_name?: string;
  account_type?: 'student' | 'instructor';
  instructor_code?: string;
}

// ── API Responses ─────────────────────────────────────────────────────────────

export interface ApiResponse<T> {
  success: boolean;
  data: T;
  message?: string;
}

export interface ApiError {
  success: false;
  message: string;
  errors?: Record<string, string[]>;
}

export interface PaginationMeta {
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
}

// ── Courses ───────────────────────────────────────────────────────────────────

export interface CoursePrice {
  online: number;
  physical_monthly: number;
  physical_quarterly: number;
  currency: string;
}

export interface CourseCategory {
  id: number;
  name: string;
  slug: string;
  icon?: string;
  courses_count?: number;
}

export interface LessonSummary {
  id: number;
  title: string;
  type: string;
  duration_minutes: number | null;
  is_free_preview: boolean;
  sort_order: number;
}

export interface ModuleSummary {
  id: number;
  title: string;
  description?: string;
  sort_order: number;
  lessons: LessonSummary[];
}

export interface Course {
  id: number;
  title: string;
  slug: string;
  short_description: string | null;
  description?: string;
  thumbnail_url: string | null;
  level: string | null;
  type: string | null;
  duration_hours: number | null;
  total_lessons: number;
  total_students: number;
  has_certificate: boolean;
  is_free: boolean;
  price: CoursePrice;
  category: { id: number; name: string; slug: string } | null;
  published_at: string | null;
  // detailed
  learning_outcomes?: string | null;
  requirements?: string | null;
  brochure_url?: string | null;
  installment_options?: InstallmentOption[];
  modules?: ModuleSummary[];
  tags?: string[];
}

export interface InstallmentOption {
  installments: number;
  per_payment: number;
  total: number;
}

// ── Enrollments ────────────────────────────────────────────────────────────────

export interface Enrollment {
  id: number;
  status: 'pending' | 'active' | 'completed' | 'cancelled' | 'expired';
  payment_status: 'unpaid' | 'pending' | 'paid' | 'failed' | 'refunded';
  payment_type: 'full' | 'installment' | null;
  training_type: string | null;
  training_label: string | null;
  amount_paid: number;
  progress_percent: number;
  enrolled_at: string | null;
  completed_at: string | null;
  expires_at: string | null;
  has_access: boolean;
  course: {
    id: number;
    title: string;
    slug: string;
    thumbnail_url: string | null;
    level: string | null;
    category: string | null;
  } | null;
}

// ── Lessons ────────────────────────────────────────────────────────────────────

export interface Lesson {
  id: number;
  title: string;
  type: string;
  content: string | null;
  video_url: string | null;
  video_provider: string | null;
  duration_minutes: number | null;
  is_free_preview: boolean;
  attachment_url: string | null;
  module: { id: number; title: string } | null;
  progress: {
    is_completed: boolean;
    watch_time_seconds: number;
    completed_at: string | null;
  } | null;
  is_bookmarked: boolean;
  note: { id: number; content: string } | null;
}

// ── Notifications ──────────────────────────────────────────────────────────────

export interface Notification {
  id: number;
  title: string;
  message: string;
  type: string;
  is_read: boolean;
  url: string | null;
  data: Record<string, unknown> | null;
  created_at: string;
  read_at: string | null;
}

// ── Certificates ───────────────────────────────────────────────────────────────

export interface Certificate {
  id: number;
  certificate_number: string;
  issued_at: string | null;
  expires_at: string | null;
  course: {
    id: number;
    title: string;
    slug: string;
    thumbnail_url: string | null;
  } | null;
  file_url?: string | null;
  verification_url?: string;
}

// ── Navigation ────────────────────────────────────────────────────────────────

export type AuthStackParamList = {
  Login:           undefined;
  Register:        undefined;
  ForgotPassword:  undefined;
};

export type MainTabParamList = {
  Home:       undefined;
  Explore:    undefined;
  MyLearning: undefined;
  Downloads:  undefined;
  Profile:    undefined;
};

export type RootStackParamList = {
  Auth:                undefined;
  Main:                undefined;
  CourseDetail:        { slug: string };
  LessonView:          { lessonId: number; courseSlug: string };
  QuizView:            { quizId: number; courseSlug: string };
  AssignmentView:      { assignmentId: number; courseSlug: string };
  ProfileEdit:         undefined;
  Notifications:       undefined;
  Certificates:        undefined;
  Security:            undefined;
  PaymentHistory:      undefined;
  LearningStats:       undefined;
  PaymentWebView:      { url: string; reference: string };
  InstructorDashboard: undefined;
};
