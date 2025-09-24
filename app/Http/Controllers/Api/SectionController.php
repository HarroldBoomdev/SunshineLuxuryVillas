<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Section;
use Illuminate\Http\Request;

class SectionController extends Controller
{
   public function show($slug)
    {
        $section = Section::where('slug', $slug)->first();

        if (!$section) {
            return response()->json(['error' => 'Section not found'], 404);
        }

        // ✅ Only decode if it's a string
        $data = is_string($section->data) ? json_decode($section->data, true) : $section->data;

        return response()->json($data);
    }

}

