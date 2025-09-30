<?php
use Illuminate\Http\Request;
use App\Models\PropertiesModel as Property;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PropertiesController;
use App\Http\Controllers\ClientsController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\FloorplanController;
use App\Http\Controllers\DealsController;
use App\Http\Controllers\DiaryController;
use App\Http\Controllers\DeveloperController;
use App\Http\Controllers\AgentsController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AccessLogController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MatchesController;
use App\Exports\PropertiesExport;
use App\Exports\ClientsExport;
use App\Exports\DevelopersExport;
use App\Http\Controllers\ViewingController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\DashboardController;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\ReportExportController;
use App\Http\Controllers\PropertyMapController;
use App\Http\Controllers\BankController;

use Spatie\Permission\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\SectionAdminController;
use App\Http\Controllers\Admin\SectionController;

use App\Http\Controllers\InboxController;
use App\Http\Middleware\Authenticate as AppAuthenticate;
use App\Http\Controllers\Reports\HistoricalListingsController;
use App\Http\Controllers\FeaturedPropertyController;

Route::get('/check-memory', function () {
    dd(ini_get('memory_limit'));
});


// Route for Admins only
Route::get('/admin', function () {
    return 'Welcome, Admin!';
})->middleware('role:Admin');

Route::get('/test-map', function () {
    return view('test-map');
});


// Route for Editors only
Route::get('/editor', function () {
    return 'Welcome, Editor!';
})->middleware('role:Editor');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/test-role', function () {
    return 'Middleware works!';
})->middleware('role:Admin');

// Admin Role Permissions Routes
Route::middleware(['auth', 'role:Admin'])->group(function () {
    Route::get('/roles', [RolePermissionController::class, 'index'])->name('roles.index');
    Route::put('/roles/{id}', [RolePermissionController::class, 'update'])->name('roles.update');
    Route::post('/roles/store', [RolePermissionController::class, 'store'])->name('roles.store');
    Route::get('/roles/{role}', [RolePermissionController::class, 'show'])->name('roles.show');
    Route::get('/roles/{role}/edit', [RolePermissionController::class, 'edit'])->name('roles.edit');
    Route::delete('/roles/{role}', [RolePermissionController::class, 'destroy'])->name('roles.destroy');
});


// Export route
Route::get('/clients/export', [ClientsController::class, 'export'])->name('clients.export');
Route::get('/properties/export', [PropertiesController::class, 'export'])->name('properties.export');
Route::get('/developers/export', [DeveloperController::class, 'export'])->name('developers.export');


Route::get('/roles/{role}', [RolePermissionController::class, 'show'])->name('roles.show');
Route::get('/roles/{user}/edit', [UserController::class, 'edit'])->name('roles.edit');
Route::put('/roles/{user}', [UserController::class, 'update'])->name('roles.update');
Route::post('/roles/store', [UserController::class, 'store'])->name('roles.store');
Route::delete('/roles/{user}', [UserController::class, 'destroy'])->name('roles.destroy');



// Home Route
Route::get('/', function () {
    return view('auth.login');
});



// Authenticated Routes
Route::middleware('auth')->group(function () {
    // Profile Routes
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    // Sidebar Routes
    Route::prefix('menu')->group(function () {
        // Add menu-related routes here
    });

    // Properties Routes
    Route::resource('properties', PropertiesController::class);
    Route::get('/properties', [PropertiesController::class, 'index'])->name('properties.index');
    // Route::post('/new-property-form', [PropertiesController::class, 'store'])->name('properties.store');
    Route::post('/new-property-form', [PropertiesController::class, 'store'])->name('properties.custom_store');
    Route::get('/properties/{id}/edit', [PropertiesController::class, 'edit'])->name('properties.edit');
    Route::get('/new-property-form', [PropertiesController::class, 'create'])->name('properties.create');
    Route::get('/properties/{id}', [PropertiesController::class, 'show'])->name('properties.show');
    Route::post('/upload-images', [GalleryController::class, 'upload'])->name('gallery.upload');
    Route::post('/upload-plan', [FloorplanController::class, 'upload'])->name('floorplan.upload');
    Route::delete('/properties/{id}', [PropertiesController::class, 'destroy'])->name('properties.destroy');
    Route::get('/properties/{id}/download-images', [PropertiesController::class, 'downloadImages'])->name('properties.download-images');





    // Clients Routes
    Route::resource('clients', ClientsController::class);
    Route::get('/clients', [ClientsController::class, 'index'])->name('clients.index');
    // Route::post('/new-client-form', [ClientsController::class, 'store'])->name('clients.store');
    Route::post('/new-client-form', [ClientsController::class, 'store'])->name('clients.custom_store');
    Route::get('/new-client-form', [ClientsController::class, 'create'])->name('clients.create');
    Route::get('/clients/{id}', [ClientsController::class, 'show'])->name('clients.show');
    Route::post('/clients/matches/{id}/update-status', [ClientsController::class, 'updateMatchStatus'])->name('matches.update-status');
    Route::get('/clients/{client}/edit', [ClientsController::class, 'edit'])->name('clients.edit');
    Route::put('/clients/{client}', [ClientsController::class, 'update'])->name('clients.update');
    Route::get('/create', [ClientsController::class, 'create'])->name('create');
    Route::post('/store', [ClientsController::class, 'store'])->name('store');
    Route::get('clients/{client}/matches', [ClientController::class, 'matches'])->name('clients.matches');


    //Viewing
    Route::get('viewing/create', [ViewingController::class, 'create'])->name('viewing.create');
    Route::post('viewing', [ViewingController::class, 'store'])->name('viewing.store');



    // Deals Routes

    Route::get('/deals', [DealsController::class, 'index'])->name('deals.index');
    Route::post('/update-deal-stage', [DealsController::class, 'updateStage'])->name('deals.updateStage');

    Route::post('/deals', [DealsController::class, 'store'])->name('deals.store');
    Route::get('/deals/{id}', [DealsController::class, 'show']);
    Route::put('/deals/{id}', [DealsController::class, 'update']);
    Route::delete('/deals/{id}', [DealsController::class, 'destroy']);
    Route::get('/api/deals', [DealsController::class, 'getDeals']);
    Route::put('/api/deals/{id}', [DealsController::class, 'updateDealStage']);

    // Diary routes
    Route::middleware(['auth'])->group(function () {
        Route::get('/diary', [DiaryController::class, 'index'])->name('diary.index');
        Route::post('/diary/store', [DiaryController::class, 'store'])->name('diary.store');
        Route::get('/diary/{id}', [DiaryController::class, 'show'])->name('diary.show');
        Route::get('/diary/{id}/edit', [DiaryController::class, 'edit'])->name('diary.edit');
        Route::put('/diary/{id}', [DiaryController::class, 'update'])->name('diary.update');
        Route::delete('/diary/{id}', [DiaryController::class, 'destroy'])->name('diary.destroy');
    });


    // Route::get('/clients/search', [ClientsController::class, 'search']);


    // Developers Routes
    Route::resource('developers', DeveloperController::class);
    Route::get('/developers', [DeveloperController::class, 'index'])->name('developers.index');
    Route::get('/developers/create', [DeveloperController::class, 'create'])->name('developers.create');
    Route::post('/developers', [DeveloperController::class, 'store'])->name('developers.store');
    Route::get('/developers/{id}', [DeveloperController::class, 'show'])->name('developers.details');
    Route::delete('/developers/{id}', [DeveloperController::class, 'destroy']);

    // Agents Routes
    Route::resource('agents', AgentsController::class);
    Route::get('/agents', [AgentsController::class, 'index'])->name('agents.index');
    Route::get('/agents/create', [AgentsController::class, 'create'])->name('agents.create');
    Route::post('/agents', [AgentsController::class, 'store'])->name('agents.store');
    Route::get('/agents/{id}', [AgentsController::class, 'agents.details']);
    Route::put('/agents/{id}', [AgentsController::class, 'update'])->name('agents.update');
    Route::delete('/agents/{id}', [AgentsController::class, 'destroy']);

    // Matchec Routes
    Route::resource('matches', MatchesController::class);
    Route::get('/matches', [MatchesController::class, 'index'])->name('matches.index');
    Route::get('/clients/{id}/matches', [MatchesController::class, 'index'])->name('clients.matches');
    Route::get('clients/{client}/matches', [MatchesController::class, 'index'])->name('clients.matches');
    Route::post('matches/store', [MatchesController::class, 'store'])->name('matches.store');
    Route::post('matches/{match}/update-status', [MatchesController::class, 'updateStatus'])->name('matches.update-status');

    Route::get('/clients/search', [App\Http\Controllers\ClientsController::class, 'search'])->name('clients.search');

    // Report
    Route::get('/report', [ReportController::class, 'index'])->name('report.index');
    Route::get('/report/section/{section}', [ReportController::class, 'loadSection']);



    // API doc
    Route::get('/api-docs', function () {
        return view('api.docs');
    })->middleware('auth')->name('api.docs');



    // Audit Log Routes
    Route::prefix('audit-logs')->group(function () {
        Route::get('/', [AuditLogController::class, 'index'])->name('audit.index');
        Route::get('/{id}', [AuditLogController::class, 'show'])->name('audit.show');
    });

    // Access Log Routes
    Route::prefix('access-logs')->group(function () {
        Route::get('/', [AccessLogController::class, 'index'])->name('access.index');
        Route::get('/{id}', [AccessLogController::class, 'show'])->name('access.show');
    });

    // Partial report
    Route::get('/report/partials/{type}', function ($type) {
        if (view()->exists("report.partials.$type")) {
            return view("report.partials.$type");
        }
        return response("<h1 class='text-red-600'>No report view available for \"$type\".</h1>", 404);
    });



   Route::get('/export/{type}/csv', [ReportExportController::class, 'exportCsv'])->name('export.listings.csv');
    Route::get('/export/{type}/pdf', [ReportExportController::class, 'exportPdf'])->name('export.listings.pdf');


    Route::get('/dashboard/sales-listings', [\App\Http\Controllers\DashboardController::class, 'salesListings'])->name('dashboard.sales.listings');
    Route::get('/dashboard/listings-only', [\App\Http\Controllers\DashboardController::class, 'listingsOnly'])->name('dashboard.listings.only');

    Route::post('/dashboard/preferences', [DashboardController::class, 'savePreferences'])->name('dashboard.preferences');

    Route::post('/diaries', [DiaryController::class, 'store'])->name('diaries.store');

    Route::get('/admin/roles', [RolePermissionController::class, 'index'])->name('admin.roles');





    Route::get('/api/properties/map', [App\Http\Controllers\PropertyMapController::class, 'index']);
    Route::get('/properties/map', [PropertyMapController::class, 'index']);


    Route::middleware(['auth'])->prefix('admin')->group(function () {
        Route::get('sections', [SectionAdminController::class, 'index'])->name('admin.sections.index');
        Route::get('sections/{slug}/edit', [SectionAdminController::class, 'edit'])->name('admin.sections.edit');
        Route::post('sections/{slug}/edit', [SectionAdminController::class, 'update'])->name('admin.sections.update');
    });

    Route::prefix('admin/sections')->name('admin.sections.')->group(function () {
        Route::get('/', [SectionController::class, 'index'])->name('index');
        Route::get('/{slug}/edit', [SectionController::class, 'edit'])->name('edit');
        Route::post('/{slug}', [SectionController::class, 'update'])->name('update');
    });

    //Historical report
    Route::prefix('report')->group(function () {
        Route::get('/report/historical-listings', [HistoricalListingsController::class, 'index'])
    ->name('reports.historical');
        Route::get('/historical-listings', [H::class, 'index'])->name('reports.historical');
        Route::get('/historical-listings.csv', [H::class, 'csv'])->name('reports.historical.csv');
        Route::get('/historical-listings.pdf', [H::class, 'pdf'])->name('reports.historical.pdf');
    });


    Route::middleware(['auth'])->group(function () {
        Route::get('/sections', [SectionController::class, 'index'])->name('sections.index');
        Route::get('/sections/{slug}/edit', [SectionController::class, 'edit'])->name('sections.edit');
        Route::put('/sections/{slug}', [SectionController::class, 'update'])->name('sections.update');
    });

    Route::delete('/properties/{id}', [PropertiesController::class, 'destroy'])->name('properties.destroy');

    //Banks
    Route::get('/banks', [BankController::class, 'index'])->name('banks.index');


    Route::middleware(['auth', 'can:manage-sections'])->group(function () {
        Route::get('/inbox', [InboxController::class, 'index'])->name('inbox.index');
        Route::get('/inbox/{submission}', [\App\Http\Controllers\InboxController::class, 'show'])->name('inbox.show');
        Route::delete('/inbox/{id}', [\App\Http\Controllers\InboxController::class, 'destroy'])->name('inbox.destroy');
        Route::post('/inbox/{id}/send', [InboxController::class, 'send'])->name('inbox.send');
    });

    Route::get('/inbox/request-callback', [\App\Http\Controllers\InboxController::class, 'requestCallback'])
    ->name('inbox.request_callback');

    Route::prefix('inbox')->group(function () {
    // specific tab pages FIRST
    Route::get('/request-callback', [InboxController::class, 'requestCallback'])
        ->name('inbox.request_callback');
    Route::get('/property-details', [InboxController::class, 'propertyDetails'])
        ->name('inbox.property_details');
    Route::get('/investor-club', [InboxController::class, 'investorClub'])
        ->name('inbox.investor_club');

    // show a single submission by numeric ID (keep this LAST)
    Route::get('/{submissionId}', [InboxController::class, 'show'])
        ->whereNumber('submissionId')
        ->name('inbox.show');


    Route::get('/admin/properties/lookup-by-refs', [\App\Http\Controllers\PropertiesController::class, 'lookupByRefs'])
        ->name('admin.properties.lookupByRefs');

    //Top 12
    Route::post('/admin/featured-properties/save', [FeaturedPropertyController::class, 'save'])
        ->name('admin.featured.save')
        ->middleware(['auth']);
        
    Route::get('/properties/picker', function (Request $request) {
        $q = trim($request->query('q', ''));

        $items = Property::query()
            ->select('id', 'reference', 'title', 'location')
            ->when($q !== '', function ($query) use ($q) {
                $query->where('reference', 'like', "%{$q}%")
                    ->orWhere('title', 'like', "%{$q}%")
                    ->orWhere('location', 'like', "%{$q}%");
            })
            ->orderByDesc('id')
            ->limit(50)
            ->get()
            ->map(function ($p) {
                return [
                    'id'        => $p->id,
                    'reference' => strtoupper($p->reference ?? ''),
                    'title'     => $p->title ?? 'Untitled',
                    'location'  => $p->location ?? 'N/A',
                ];
            });

        return response()->json(['items' => $items]);
    })->name('properties.picker');

    // resource route comes after
    Route::resource('properties', PropertiesController::class);

});

});

require __DIR__.'/auth.php';
