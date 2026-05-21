<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    protected $table = 'notifications_log';
    protected $fillable = ['user_id', 'title', 'message', 'type', 'channel', 'is_read', 'url', 'data', 'read_at'];
    protected $casts = ['is_read' => 'boolean', 'data' => 'array', 'read_at' => 'datetime'];

    public function user() { return $this->belongsTo(User::class); }
}
