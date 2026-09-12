<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password as PasswordRules;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with('roles')
            ->when($request->search, fn($q, $s) => $q->where(fn($q) => $q->where('first_name', 'like', "%$s%")->orWhere('last_name', 'like', "%$s%")->orWhere('email', 'like', "%$s%")))
            ->when($request->role, fn($q, $r) => $q->whereHas('roles', fn($q) => $q->where('name', $r)))
            ->when($request->status, fn($q, $st) => $q->where('status', $st))
            ->orderByDesc('created_at')
            ->paginate(20);

        $roles = Role::all();
        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => 'required|email|unique:users',
            'phone'      => 'nullable|string|max:20',
            'password'   => 'required|min:8',
            'role'       => 'required|exists:roles,name',
            'status'     => 'required|in:active,inactive,pending,suspended',
        ]);

        $user = User::create(array_merge($data, ['email_verified_at' => now()]));
        $user->assignRole($data['role']);
        return redirect()->route('admin.users.index')->with('success', 'User created!');
    }

    public function show(User $user)
    {
        $user->load(['roles', 'enrollments.course', 'payments']);
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'first_name'   => 'required|string|max:100',
            'last_name'    => 'required|string|max:100',
            'email'        => 'required|email|unique:users,email,' . $user->id,
            'phone'        => 'nullable|string|max:20',
            'status'       => 'required|in:active,inactive,pending,suspended',
            'role'         => 'required|exists:roles,name',
            'new_password' => ['nullable', 'confirmed', PasswordRules::min(8)->mixedCase()->numbers()->symbols()],
        ]);

        if (!empty($data['new_password'])) {
            $data['password'] = \Illuminate\Support\Facades\Hash::make($data['new_password']);
        }
        unset($data['new_password'], $data['new_password_confirmation']);

        $user->update($data);
        $user->syncRoles([$data['role']]);
        return back()->with('success', 'User updated!');
    }

    public function destroy(User $user)
    {
        if ($user->isSuperAdmin()) abort(403, 'Cannot delete super admin.');
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted!');
    }

    public function toggleStatus(int $id)
    {
        $user = User::findOrFail($id);
        $user->update(['status' => $user->status === 'active' ? 'suspended' : 'active']);
        return response()->json(['status' => $user->status]);
    }

    public function export(Request $request): StreamedResponse
    {
        $query = User::with('roles')
            ->when($request->role, fn($q, $r) => $q->whereHas('roles', fn($q) => $q->where('name', $r)))
            ->when($request->status, fn($q, $st) => $q->where('status', $st))
            ->orderByDesc('created_at');

        return response()->stream(function () use ($query) {
            echo implode(',', ['ID', 'First Name', 'Last Name', 'Email', 'Phone', 'Role', 'Status', 'Admission No', 'Country', 'Registered At']) . "\n";
            $query->chunk(500, function ($users) {
                foreach ($users as $user) {
                    echo implode(',', array_map(fn($v) => '"' . str_replace('"', '""', $v ?? '') . '"', [
                        $user->id, $user->first_name, $user->last_name, $user->email,
                        $user->phone ?? '', $user->roles->pluck('name')->join(', '),
                        $user->status, $user->admission_number ?? '',
                        $user->country ?? '', $user->created_at->format('Y-m-d H:i:s'),
                    ])) . "\n";
                }
            });
        }, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="users_export_' . now()->format('Y-m-d') . '.csv"',
            'Cache-Control'       => 'no-cache',
        ]);
    }
}
