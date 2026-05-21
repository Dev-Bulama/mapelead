<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PageView extends Model
{
    protected $fillable = ['url', 'title', 'user_id', 'session_id', 'ip_address', 'country', 'device', 'browser', 'referrer'];
}
