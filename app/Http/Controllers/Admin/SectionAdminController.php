<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Section;
use Illuminate\Http\Request;

class SectionAdminController extends Controller
{
    public function index()
    {
        $sections = Section::all();
        return view('admin.sections.index', compact('sections'));
    }

    public function edit($slug)
    {
        $section = Section::where('slug', $slug)->firstOrFail();
        $content = json_decode($section->data, true);
        return view('admin.sections.edit', compact('section', 'content'));
    }

    public function update(Request $request, $slug)
    {
        $section = Section::where('slug', $slug)->firstOrFail();
        $data = $request->except(['_token']);
        $section->update(['data' => json_encode($data)]);
        return redirect()->route('admin.sections.index')->with('success', 'Section updated!');
    }
}
