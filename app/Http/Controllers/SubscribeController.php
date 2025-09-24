<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubscribeController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'email'  => ['required','email', Rule::unique('newsletter_subscribers','email')],
            'source' => ['nullable','string','max:100'],
        ]);

        $row = NewsletterSubscriber::create($data);

        return response()->json([
            'success' => true,
            'id'      => $row->id,
        ]);
    }
}
