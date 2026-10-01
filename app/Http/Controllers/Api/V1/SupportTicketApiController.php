<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\SupportTicket;
use App\Models\TicketReply;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupportTicketApiController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request): JsonResponse
    {
        $tickets = SupportTicket::where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($t) => $this->formatTicket($t));

        return $this->success($tickets);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'subject'     => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'category'    => 'nullable|string|max:100',
            'priority'    => 'nullable|in:low,normal,high,urgent',
        ]);

        $ticket = SupportTicket::create([
            'user_id'     => $request->user()->id,
            'subject'     => $data['subject'],
            'description' => $data['description'],
            'category'    => $data['category'] ?? null,
            'priority'    => $data['priority'] ?? 'normal',
            'status'      => 'open',
        ]);

        return $this->success($this->formatTicket($ticket), message: 'Ticket created successfully');
    }

    public function show(Request $request, int $ticketId): JsonResponse
    {
        $ticket = SupportTicket::where('id', $ticketId)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$ticket) {
            return $this->error('Ticket not found', 404);
        }

        $replies = TicketReply::where('ticket_id', $ticketId)
            ->where('is_internal', false)
            ->with('user:id,full_name,avatar_url,roles')
            ->orderBy('created_at')
            ->get()
            ->map(fn ($r) => [
                'id'         => $r->id,
                'message'    => $r->message,
                'is_staff'   => $r->user?->hasRole('admin') || $r->user?->hasRole('super_admin') || $r->user?->hasRole('support'),
                'author'     => $r->user ? ['full_name' => $r->user->full_name, 'avatar_url' => $r->user->avatar_url] : null,
                'created_at' => $r->created_at,
            ]);

        return $this->success([
            ...$this->formatTicket($ticket),
            'replies' => $replies,
        ]);
    }

    public function reply(Request $request, int $ticketId): JsonResponse
    {
        $ticket = SupportTicket::where('id', $ticketId)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$ticket) {
            return $this->error('Ticket not found', 404);
        }

        if (in_array($ticket->status, ['resolved', 'closed'])) {
            return $this->error('Cannot reply to a closed ticket', 422);
        }

        $data = $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        $reply = TicketReply::create([
            'ticket_id'   => $ticketId,
            'user_id'     => $request->user()->id,
            'message'     => $data['message'],
            'is_internal' => false,
        ]);

        if ($ticket->status === 'resolved') {
            $ticket->update(['status' => 'open']);
        }

        return $this->success([
            'id'         => $reply->id,
            'message'    => $reply->message,
            'is_staff'   => false,
            'author'     => ['full_name' => $request->user()->full_name, 'avatar_url' => $request->user()->avatar_url],
            'created_at' => $reply->created_at,
        ], message: 'Reply sent');
    }

    public function close(Request $request, int $ticketId): JsonResponse
    {
        $ticket = SupportTicket::where('id', $ticketId)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$ticket) {
            return $this->error('Ticket not found', 404);
        }

        $ticket->update([
            'status'      => 'closed',
            'resolved_at' => now(),
        ]);

        return $this->success($this->formatTicket($ticket->fresh()), message: 'Ticket closed');
    }

    private function formatTicket(SupportTicket $ticket): array
    {
        return [
            'id'            => $ticket->id,
            'ticket_number' => $ticket->ticket_number,
            'subject'       => $ticket->subject,
            'description'   => $ticket->description,
            'category'      => $ticket->category,
            'priority'      => $ticket->priority,
            'status'        => $ticket->status,
            'created_at'    => $ticket->created_at,
            'updated_at'    => $ticket->updated_at,
            'resolved_at'   => $ticket->resolved_at,
        ];
    }
}
