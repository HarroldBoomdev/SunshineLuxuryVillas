<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Section;

class SectionController extends Controller
{
    public function index()
    {
        $sections = Section::all();
        return view('admin.sections.index', compact('sections'));
    }

    public function edit($slug)
    {
        $section = Section::where('slug', $slug)->firstOrFail();
        return view('admin.sections.edit', compact('section'));
    }

    public function update(Request $request, $slug)
    {
        $section = Section::where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'background_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = [
            'title' => $validated['title'] ?? '',
            'subtitle' => $validated['subtitle'] ?? '',
            'background_image' => json_decode($section->data, true)['background_image'] ?? '',
        ];

        if ($request->hasFile('background_image')) {
            $path = $request->file('background_image')->store('uploads/sections', 'public');
            $data['background_image'] = '/storage/' . $path;
        }

        $section->data = json_encode($data);
        $section->save();

        return redirect()->route('sections.index')->with('success', 'Section updated successfully!');
    }


}

