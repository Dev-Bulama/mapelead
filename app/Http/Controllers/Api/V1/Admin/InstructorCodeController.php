<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\InstructorCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InstructorCodeController extends Controller
{
    use ApiResponseTrait;

    public function index(): JsonResponse
    {
        $codes = InstructorCode::with([
            'createdBy:id,full_name',
            'usedBy:id,full_name,email',
        ])
            ->orderByDesc('created_at')
            ->paginate(25);

        return $this->success($codes);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'note'  => 'nullable|string|max:255',
            'count' => 'integer|min:1|max:50',
        ]);

        $count = $request->integer('count', 1);
        $created = [];

        for ($i = 0; $i < $count; $i++) {
            do {
                $code = strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(4));
            } while (InstructorCode::where('code', $code)->exists());

            $created[] = InstructorCode::create([
                'code'               => $code,
                'note'               => $request->note,
                'is_active'          => true,
                'created_by_user_id' => $request->user()->id,
            ]);
        }

        return $this->success(
            count($created) === 1 ? $created[0] : $created,
            201,
            count($created) . ' code(s) generated'
        );
    }

    public function destroy(InstructorCode $instructorCode): JsonResponse
    {
        if ($instructorCode->used_at) {
            return $this->error('Cannot revoke a code that has already been used.', 422);
        }

        $instructorCode->update(['is_active' => false]);

        return $this->success(['revoked' => true]);
    }
}
