<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactForm;
use App\Models\Lead;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $leads = Lead::with('assignedTo')
            ->when($request->search, fn($q, $s) => $q->where(fn($q) => $q->where('email', 'like', "%$s%")->orWhere('first_name', 'like', "%$s%")))
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->source, fn($q, $s) => $q->where('source', $s))
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.leads.index', compact('leads'));
    }

    public function show(Lead $lead)
    {
        return view('admin.leads.show', compact('lead'));
    }

    public function edit(Lead $lead)
    {
        return view('admin.leads.edit', compact('lead'));
    }

    public function update(Request $request, Lead $lead)
    {
        $data = $request->validate([
            'status'  => 'required|in:new,contacted,qualified,enrolled,lost',
            'notes'   => 'nullable|string|max:2000',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $lead->update(array_merge($data, ['last_contacted_at' => now()]));
        return back()->with('success', 'Lead updated!');
    }

    public function destroy(Lead $lead)
    {
        $lead->delete();
        return redirect()->route('admin.leads.index')->with('success', 'Lead deleted!');
    }

    public function newsletter()
    {
        $subscribers = NewsletterSubscriber::orderByDesc('created_at')->paginate(20);
        return view('admin.leads.newsletter', compact('subscribers'));
    }

    public function contacts()
    {
        $contacts = ContactForm::orderByDesc('created_at')->paginate(20);
        return view('admin.leads.contacts', compact('contacts'));
    }
}
