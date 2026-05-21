<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class NewsletterSubscriber extends Model
{
    protected $fillable = ['email', 'name', 'source', 'status', 'token', 'confirmed_at', 'unsubscribed_at'];
    protected $casts = ['confirmed_at' => 'datetime', 'unsubscribed_at' => 'datetime'];

    public static function subscribe(string $email, ?string $name = null, ?string $source = null): self
    {
        return static::firstOrCreate(
            ['email' => $email],
            ['name' => $name, 'source' => $source, 'token' => Str::random(40), 'status' => 'subscribed']
        );
    }
}
