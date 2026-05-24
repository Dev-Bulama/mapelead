<?php

namespace App\Http\Controllers\Admin;

use App\Models\EmailTemplate;
use App\Services\CMS\EmailTemplateService;
use Illuminate\Http\Request;

class EmailTemplateController extends \App\Http\Controllers\Controller
{
    public function index()
    {
        $templates = EmailTemplate::paginate(20);

        return view('admin.email-templates.index', compact('templates'));
    }

    public function create()
    {
        return view('admin.cms.email-templates.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                => 'required',
            'slug'                => 'required|unique:email_templates,slug',
            'subject'             => 'required',
            'body_html'           => 'required',
            'available_variables' => 'nullable|array',
        ]);

        EmailTemplate::create(array_merge($validated, [
            'is_system' => false,
            'is_active' => true,
        ]));

        return redirect()->route('admin.email-templates.index')
            ->with('success', 'Email template created successfully.');
    }

    public function edit(EmailTemplate $emailTemplate)
    {
        return view('admin.cms.email-templates.edit', compact('emailTemplate'));
    }

    public function update(Request $request, EmailTemplate $emailTemplate)
    {
        $validated = $request->validate([
            'name'                => 'required',
            'subject'             => 'required',
            'body_html'           => 'required',
            'available_variables' => 'nullable|array',
        ]);

        if (! $emailTemplate->is_system) {
            $validated['slug'] = $emailTemplate->slug;
        }

        $emailTemplate->update($validated);

        return redirect()->back()->with('success', 'Email template updated successfully.');
    }

    public function preview(Request $request, EmailTemplate $emailTemplate)
    {
        $sampleVariables = [
            'student_name'     => 'John Doe',
            'balance'          => '50,000',
            'due_date'         => now()->format('M d, Y'),
            'course_name'      => 'Sample Course',
            'admission_number' => 'MAP/2026/0001',
        ];

        return view('admin.cms.email-templates.preview', compact('emailTemplate', 'sampleVariables'));
    }

    public function toggleActive(EmailTemplate $emailTemplate)
    {
        $emailTemplate->update(['is_active' => ! $emailTemplate->is_active]);

        return redirect()->back()->with('success', 'Email template status updated.');
    }

    public function destroy(EmailTemplate $emailTemplate)
    {
        if ($emailTemplate->is_system) {
            return redirect()->back()->with('error', 'System templates cannot be deleted.');
        }

        $emailTemplate->delete();

        return redirect()->back()->with('success', 'Email template deleted successfully.');
    }
}
