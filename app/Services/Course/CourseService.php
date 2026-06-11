<?php
namespace App\Services\Course;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Instructor;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class CourseService
{
    public function getAllPublished(array $filters = []): LengthAwarePaginator
    {
        $query = Course::published()
            ->with(['instructor.user', 'category'])
            ->when($filters['category'] ?? null, fn($q, $cat) => $q->whereHas('category', fn($q) => $q->where('slug', $cat)->orWhere('id', (int) $cat)))
            ->when($filters['type'] ?? null, fn($q, $t) => $q->where('type', $t))
            ->when($filters['level'] ?? null, fn($q, $l) => $q->where('level', $l))
            ->when($filters['search'] ?? null, fn($q, $s) => $q->where(fn($q) => $q->where('title', 'like', "%{$s}%")->orWhere('short_description', 'like', "%{$s}%")))
            ->when($filters['price_min'] ?? null, fn($q, $p) => $q->where('price', '>=', $p))
            ->when($filters['price_max'] ?? null, fn($q, $p) => $q->where('price', '<=', $p))
            ->when($filters['free_only'] ?? null, fn($q) => $q->where('is_free', true))
            ->when($filters['certificate'] ?? null, fn($q) => $q->where('certificate_enabled', true));

        $sort = $filters['sort'] ?? 'popular';
        match ($sort) {
            'newest'   => $query->orderByDesc('published_at'),
            'price_low'=> $query->orderBy('price'),
            'price_high'=> $query->orderByDesc('price'),
            'rating'   => $query->orderByDesc('average_rating'),
            default    => $query->orderByDesc('total_students'),
        };

        return $query->paginate($filters['per_page'] ?? 12);
    }

    public function getFeatured(int $limit = 6): Collection
    {
        return Course::published()->featured()
            ->with(['instructor.user', 'category'])
            ->limit($limit)
            ->get();
    }

    public function findBySlug(string $slug): ?Course
    {
        return Course::published()
            ->with(['instructor.user', 'category', 'modules.lessons', 'reviews.user', 'tags', 'courseInstructors.instructor.user'])
            ->where('slug', $slug)
            ->firstOrFail();
    }

    public function create(array $data): Course
    {
        $course = Course::create($data);

        if (!empty($data['tags'])) {
            $course->tags()->sync($data['tags']);
        }

        return $course->load(['instructor', 'category']);
    }

    public function update(Course $course, array $data): Course
    {
        $course->update($data);

        if (isset($data['tags'])) {
            $course->tags()->sync($data['tags']);
        }

        return $course->fresh(['instructor', 'category']);
    }

    public function publish(Course $course): Course
    {
        $course->update([
            'status' => 'published',
            'is_published' => true,
            'published_at' => now(),
        ]);
        return $course;
    }

    public function getCategories(): Collection
    {
        return CourseCategory::active()
            ->withCount(['courses' => fn($q) => $q->published()])
            ->orderBy('sort_order')
            ->get();
    }
}
