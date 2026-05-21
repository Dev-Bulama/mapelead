<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class BlogComment extends Model
{
    protected $fillable = ['blog_post_id', 'user_id', 'parent_id', 'name', 'email', 'content', 'status', 'ip_address'];

    public function post() { return $this->belongsTo(BlogPost::class, 'blog_post_id'); }
    public function user() { return $this->belongsTo(User::class); }
    public function parent() { return $this->belongsTo(BlogComment::class, 'parent_id'); }
    public function replies() { return $this->hasMany(BlogComment::class, 'parent_id')->where('status', 'approved'); }
}
