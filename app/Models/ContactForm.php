<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ContactForm extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'subject', 'message', 'form_type', 'status', 'ip_address'];
}
