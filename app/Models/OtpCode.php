<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class OtpCode extends Model
{
    protected $fillable = ['identifier', 'type', 'code', 'used', 'expires_at'];
    protected $casts = ['used' => 'boolean', 'expires_at' => 'datetime'];
}
