<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    public function index()  { return view('admin.coming-soon'); }
    public function create() { return view('admin.coming-soon'); }
    public function store(Request $request) { return back(); }
    public function show($id) { return view('admin.coming-soon'); }
    public function edit($id) { return view('admin.coming-soon'); }
    public function update(Request $request, $id) { return back(); }
    public function destroy($id) { return back(); }
}
