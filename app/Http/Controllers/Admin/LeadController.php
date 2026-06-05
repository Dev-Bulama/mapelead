<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactForm;
use App\Models\Lead;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    public function exportLeads(): StreamedResponse
    {
        $leads = Lead::with('assignedTo')->orderByDesc('created_at')->get();
        return $this->streamCsv('leads_export_' . now()->format('Y-m-d') . '.csv', function () use ($leads) {
            echo implode(',', ['ID', 'First Name', 'Last Name', 'Email', 'Phone', 'Source', 'Interest', 'Status', 'Country', 'Notes', 'Created At']) . "\n";
            foreach ($leads as $lead) {
                echo implode(',', array_map(fn($v) => '"' . str_replace('"', '""', $v ?? '') . '"', [
                    $lead->id, $lead->first_name, $lead->last_name, $lead->email, $lead->phone ?? '',
                    $lead->source ?? '', $lead->interest ?? '', $lead->status ?? '',
                    $lead->country ?? '', $lead->notes ?? '',
                    $lead->created_at->format('Y-m-d H:i:s'),
                ])) . "\n";
            }
        });
    }

    public function exportContacts(): StreamedResponse
    {
        $contacts = ContactForm::orderByDesc('created_at')->get();
        return $this->streamCsv('contacts_export_' . now()->format('Y-m-d') . '.csv', function () use ($contacts) {
            echo implode(',', ['ID', 'Name', 'Email', 'Phone', 'Subject', 'Message', 'Form Type', 'Status', 'Created At']) . "\n";
            foreach ($contacts as $c) {
                echo implode(',', array_map(fn($v) => '"' . str_replace('"', '""', $v ?? '') . '"', [
                    $c->id, $c->name, $c->email, $c->phone ?? '', $c->subject ?? '',
                    $c->message ?? '', $c->form_type ?? 'contact', $c->status ?? 'new',
                    $c->created_at->format('Y-m-d H:i:s'),
                ])) . "\n";
            }
        });
    }

    public function exportNewsletter(): StreamedResponse
    {
        $subscribers = NewsletterSubscriber::orderByDesc('created_at')->get();
        return $this->streamCsv('newsletter_export_' . now()->format('Y-m-d') . '.csv', function () use ($subscribers) {
            echo implode(',', ['ID', 'Name', 'Email', 'Source', 'Status', 'Subscribed At']) . "\n";
            foreach ($subscribers as $s) {
                echo implode(',', array_map(fn($v) => '"' . str_replace('"', '""', $v ?? '') . '"', [
                    $s->id, $s->name ?? '', $s->email, $s->source ?? '',
                    $s->status ?? 'active', $s->created_at->format('Y-m-d H:i:s'),
                ])) . "\n";
            }
        });
    }

    private function streamCsv(string $filename, callable $callback): StreamedResponse
    {
        return response()->stream($callback, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'no-cache',
        ]);
    }
}
