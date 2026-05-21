<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupportTicket extends Model
{
    use SoftDeletes;
    protected $fillable = ['user_id', 'ticket_number', 'subject', 'description', 'priority', 'status', 'category', 'assigned_to', 'resolved_at'];
    protected $casts = ['resolved_at' => 'datetime'];

    public function user() { return $this->belongsTo(User::class); }
    public function assignedTo() { return $this->belongsTo(User::class, 'assigned_to'); }
    public function replies() { return $this->hasMany(TicketReply::class, 'ticket_id'); }
}
