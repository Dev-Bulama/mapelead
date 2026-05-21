<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ContactForm;
use App\Models\Lead;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        return view('web.contact');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email|max:200',
            'phone'   => 'nullable|string|max:20',
            'subject' => 'nullable|string|max:200',
            'message' => 'required|string|max:5000',
        ]);

        ContactForm::create(array_merge($data, ['ip_address' => $request->ip()]));

        // Also create a lead
        Lead::firstOrCreate(['email' => $data['email']], [
            'first_name'  => explode(' ', $data['name'])[0],
            'last_name'   => explode(' ', $data['name'])[1] ?? '',
            'email'       => $data['email'],
            'phone'       => $data['phone'],
            'source'      => 'contact_form',
            'source_page' => $request->header('referer'),
            'ip_address'  => $request->ip(),
        ]);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Message sent successfully!']);
        }

        return back()->with('success', 'Your message has been sent. We\'ll get back to you shortly!');
    }

    public function subscribe(Request $request)
    {
        $request->validate(['email' => 'required|email|max:200', 'name' => 'nullable|string|max:100']);

        NewsletterSubscriber::subscribe($request->email, $request->name, 'website_footer');

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Subscribed successfully!']);
        }

        return back()->with('success', 'You\'ve been subscribed to our newsletter!');
    }
}
