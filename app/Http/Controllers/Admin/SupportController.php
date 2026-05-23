<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\TicketReply;
use App\Models\User;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function index(Request $request)
    {
        $query = SupportTicket::with(['user', 'replies', 'assignedTo']);

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($priority = $request->input('priority')) {
            $query->where('priority', $priority);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('ticket_number', 'like', "%{$search}%")
                  ->orWhereHas('user', fn ($u) => $u->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%"));
            });
        }

        $tickets = $query->latest()->paginate(20)->withQueryString();

        $openCount       = SupportTicket::where('status', 'open')->count();
        $inProgressCount = SupportTicket::where('status', 'in_progress')->count();
        $resolvedCount   = SupportTicket::where('status', 'resolved')->count();
        $closedCount     = SupportTicket::where('status', 'closed')->count();

        return view('admin.support.index', compact(
            'tickets',
            'openCount',
            'inProgressCount',
            'resolvedCount',
            'closedCount'
        ));
    }

    public function show(SupportTicket $support)
    {
        $support->load(['user', 'replies.user', 'assignedTo']);
        return view('admin.support.show', compact('support'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject'     => 'required|string|max:255',
            'description' => 'required|string',
            'user_id'     => 'required|exists:users,id',
            'priority'    => 'required|in:low,medium,high',
        ]);

        SupportTicket::create([
            'user_id'       => $request->input('user_id'),
            'ticket_number' => 'TKT-' . strtoupper(\Str::random(8)),
            'subject'       => $request->input('subject'),
            'description'   => $request->input('description'),
            'priority'      => $request->input('priority'),
            'status'        => 'open',
        ]);

        return back()->with('success', 'Ticket created successfully.');
    }

    public function update(Request $request, SupportTicket $support)
    {
        $request->validate([
            'status'      => 'required|in:open,in_progress,resolved,closed',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $support->update($request->only(['status', 'assigned_to']));
        return back()->with('success', 'Ticket updated.');
    }

    public function assign(Request $request, int $id)
    {
        $request->validate(['assigned_to' => 'required|exists:users,id']);
        SupportTicket::findOrFail($id)->update(['assigned_to' => $request->input('assigned_to')]);
        return back()->with('success', 'Ticket assigned.');
    }

    public function close(Request $request, int $id)
    {
        SupportTicket::findOrFail($id)->update([
            'status'      => 'closed',
            'resolved_at' => now(),
        ]);
        return back()->with('success', 'Ticket closed.');
    }

    public function destroy(SupportTicket $support)
    {
        $support->delete();
        return back()->with('success', 'Ticket deleted.');
    }

    public function reply(Request $request, SupportTicket $support)
    {
        $request->validate(['message' => 'required|string|max:5000']);

        TicketReply::create([
            'ticket_id'   => $support->id,
            'user_id'     => auth()->id(),
            'message'     => $request->input('message'),
            'is_internal' => $request->boolean('is_internal'),
        ]);

        return back()->with('success', 'Reply sent.');
    }

    // Stub to satisfy resource route
    public function create() { return redirect()->route('admin.support.index'); }
    public function edit($id) { return redirect()->route('admin.support.index'); }
}
