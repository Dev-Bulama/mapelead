<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = Faq::orderBy('sort_order')->orderBy('id')->get();
        return view('admin.cms.faqs', compact('faqs'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'question'    => 'required|string|max:500',
            'answer'      => 'required|string',
            'category'    => 'nullable|string|max:100',
            'is_active'   => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'sort_order'  => 'nullable|integer',
        ]);

        $data['is_active']   = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');

        Faq::create($data);
        return back()->with('success', 'FAQ added successfully.');
    }

    public function update(Request $request, Faq $faq)
    {
        $data = $request->validate([
            'question'    => 'required|string|max:500',
            'answer'      => 'required|string',
            'category'    => 'nullable|string|max:100',
            'is_active'   => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'sort_order'  => 'nullable|integer',
        ]);

        $data['is_active']   = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');

        $faq->update($data);
        return back()->with('success', 'FAQ updated successfully.');
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();
        return back()->with('success', 'FAQ deleted.');
    }

    // Stubs to satisfy resource route
    public function create() { return redirect()->route('admin.faqs.index'); }
    public function show($id) { return redirect()->route('admin.faqs.index'); }
    public function edit($id) { return redirect()->route('admin.faqs.index'); }
}
