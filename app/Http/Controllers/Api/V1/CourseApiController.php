<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Course;
use App\Services\Course\CourseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourseApiController extends Controller
{
    use ApiResponseTrait;

    public function __construct(private readonly CourseService $courseService) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only([
            'category', 'type', 'level', 'search', 'min_price', 'max_price',
            'free_only', 'has_certificate', 'sort',
        ]);

        $courses = $this->courseService->getAllPublished($filters);

        return $this->success([
            'courses'  => $courses->getCollection()->map(fn($c) => $this->formatCourse($c)),
            'meta'     => [
                'current_page' => $courses->currentPage(),
                'last_page'    => $courses->lastPage(),
                'per_page'     => $courses->perPage(),
                'total'        => $courses->total(),
            ],
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $course = $this->courseService->findBySlug($slug);

        if (!$course) {
            return $this->error('Course not found', 404);
        }

        return $this->success($this->formatCourse($course, detailed: true));
    }

    public function categories(): JsonResponse
    {
        $categories = $this->courseService->getCategories();

        return $this->success(
            $categories->map(fn($cat) => [
                'id'            => $cat->id,
                'name'          => $cat->name,
                'slug'          => $cat->slug,
                'icon'          => $cat->icon,
                'courses_count' => $cat->courses_count ?? 0,
            ])->values()
        );
    }

    public function featured(): JsonResponse
    {
        $courses = $this->courseService->getFeatured(8);

        return $this->success(
            $courses->map(fn($c) => $this->formatCourse($c))->values()
        );
    }

    public function search(Request $request): JsonResponse
    {
        $request->validate(['q' => 'required|string|min:2|max:100']);

        $courses = $this->courseService->getAllPublished(['search' => $request->q]);

        return $this->success([
            'courses' => $courses->getCollection()->map(fn($c) => $this->formatCourse($c)),
            'total'   => $courses->total(),
            'query'   => $request->q,
        ]);
    }

    private function formatCourse(Course $course, bool $detailed = false): array
    {
        $data = [
            'id'                => $course->id,
            'title'             => $course->title,
            'slug'              => $course->slug,
            'short_description' => $course->short_description,
            'thumbnail_url'     => $course->thumbnail_url,
            'level'             => $course->level,
            'type'              => $course->type,
            'duration_hours'    => $course->duration_hours,
            'total_lessons'     => $course->total_lessons ?? $course->lessons()->count(),
            'total_students'    => $course->total_students ?? 0,
            'has_certificate'   => (bool) $course->has_certificate,
            'is_free'           => $course->price_online == 0 || $course->price_online === null,
            'price'             => [
                'online'             => (float) ($course->price_online ?? 0),
                'physical_monthly'   => (float) ($course->price_physical_monthly ?? 0),
                'physical_quarterly' => (float) ($course->price_physical_quarterly ?? 0),
                'currency'           => 'NGN',
            ],
            'category'          => $course->category ? [
                'id'   => $course->category->id,
                'name' => $course->category->name,
                'slug' => $course->category->slug,
            ] : null,
            'published_at'      => $course->published_at?->toDateString(),
        ];

        if ($detailed) {
            $data['description']         = $course->description;
            $data['learning_outcomes']   = $course->learning_outcomes;
            $data['requirements']        = $course->requirements;
            $data['brochure_url']        = $course->brochure_url;
            $data['installment_options'] = $course->getInstallmentOptionsWithDefaults();
            $data['modules']             = $course->modules ? $course->modules->map(fn($m) => [
                'id'          => $m->id,
                'title'       => $m->title,
                'description' => $m->description,
                'sort_order'  => $m->sort_order,
                'lessons'     => $m->lessons ? $m->lessons->map(fn($l) => [
                    'id'               => $l->id,
                    'title'            => $l->title,
                    'type'             => $l->type,
                    'duration_minutes' => $l->duration_minutes,
                    'is_free_preview'  => (bool) $l->is_free_preview,
                    'sort_order'       => $l->sort_order,
                ])->values() : [],
            ])->values() : [];
            $data['tags'] = $course->tags ? $course->tags->pluck('name')->values() : [];
        }

        $data['avg_rating']   = round($course->reviews()->avg('rating') ?? 0, 1);
        $data['review_count'] = $course->reviews()->count();

        return $data;
    }
}
