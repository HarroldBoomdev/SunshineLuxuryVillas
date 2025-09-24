<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\FormSubmission;

use App\Http\Controllers\Api\PropertiesController;
use App\Http\Controllers\ClientsController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\Api\SectionController;
use App\Http\Controllers\Api\InboxController;
use App\Http\Controllers\Form\SubscribeController as StatamicSubscribeController;
use App\Http\Controllers\Api\NewsletterController;


/**
 * Auth (example)
 */
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/**
 * Healthcheck
 */
Route::get('/ping', fn () => response()->json(['pong' => true]));

/**
 * Properties
 */
Route::get('/properties',                [PropertiesController::class, 'apiIndex']);
Route::get('/properties/featured',       [PropertiesController::class, 'featured']);
Route::get('/properties/recent',         [PropertiesController::class, 'recent']);
Route::get('/properties/filters',        [PropertiesController::class, 'filters']);
Route::get('/properties/top',            [PropertiesController::class, 'top']);

// lookup by reference
Route::get('/properties/by-ref/{ref}',   [PropertiesController::class, 'showByReference']);
Route::get('/properties/ref/{ref}',      [PropertiesController::class, 'showByReference']);
Route::post('/properties/by-ref',        [PropertiesController::class, 'findByReference']);

// fallback single property (keep AFTER specific routes)
Route::get('/properties/{reference}',    [PropertiesController::class, 'show']);

/**
 * Sections
 */
Route::get('/sections/{slug}', [SectionController::class, 'show']);
Route::get('/sections/about-testimonial-banner', fn () =>
    response()->json(optional(\App\Models\Section::where('slug', 'about-testimonial-banner')->first())->decoded_data)
);

/**
 * Search
 */
Route::get('/search', [SearchController::class, 'search']);

/**
 * Inbox (API)
 * Use the same controller for all inbox posts; UI lists via GET.
 */
Route::post('/inbox',               [InboxController::class, 'store'])->name('api.inbox.store');
Route::get('/inbox',                [InboxController::class, 'index'])->name('api.inbox.index');
// Optional alias (kept for convenience if the frontend posts here)
Route::post('/inbox/investor-club', [InboxController::class, 'store'])->name('api.inbox.store.investor');

/**
 * CORS preflight catch-all (handy during dev)
 */
Route::options('/{any}', fn () => response()->noContent(204))
    ->where('any', '.*');

    Route::post('/inbox-test', function (Request $r) {
    return response()->json([
        'ok' => true,
        'marker' => 'inbox-test',
        'received' => $r->all(),
    ], 201);
});

Route::post('/subscribe', [SubscribeController::class, 'store']);
Route::post('/subscribe', [NewsletterController::class, 'store']);

Route::post('/subscribe', function (Request $request) {
    $data = $request->validate([
        'email'  => ['required','email','max:150'],
        'source' => ['nullable','string','max:100'],
    ]);

    // Save to the Inbox table so it shows under the “Subscribe” tab
    $submission = FormSubmission::create([
        'form_key'  => 'subscribe',          // API key (underscore)
        'type'      => 'subscribe',          // UI tab slug (hyphen/slug)
        'name'      => null,
        'email'     => $data['email'],
        'phone'     => null,
        'reference' => null,
        'payload'   => ['source' => $data['source'] ?? 'footer'],
    ]);

    return response()->json([
        'success' => true,
        'id'      => $submission->id,
    ]);
});


