<?php
namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\TicketReply;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = SupportTicket::where('user_id', auth()->id())->orderByDesc('created_at')->paginate(10);
        return view('student.tickets', compact('tickets'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'subject'     => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'priority'    => 'required|in:low,medium,high',
            'category'    => 'nullable|string|max:100',
        ]);

        SupportTicket::create(array_merge($data, [
            'user_id'       => auth()->id(),
            'ticket_number' => 'TKT-' . strtoupper(Str::random(8)),
            'status'        => 'open',
        ]));

        return back()->with('success', 'Ticket submitted successfully!');
    }

    public function show(int $id)
    {
        $ticket = SupportTicket::where('user_id', auth()->id())->with(['replies.user'])->findOrFail($id);
        return view('student.ticket-show', compact('ticket'));
    }

    public function reply(int $id, Request $request)
    {
        $request->validate(['message' => 'required|string|max:5000']);
        $ticket = SupportTicket::where('user_id', auth()->id())->findOrFail($id);

        TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id'   => auth()->id(),
            'message'   => $request->message,
        ]);

        return back()->with('success', 'Reply sent!');
    }
}
