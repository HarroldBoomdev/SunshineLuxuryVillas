<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FloorplanController extends Controller
{
    public function upload(Request $request)
    {
        $images = [];
        foreach ($request->file('images') as $file) {
            $path = $file->store('uploads', 'public');
            $images[] = $path;
        }

        return response()->json(['uploadedImages' => $images]);
    }
}
