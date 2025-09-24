<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Lead;
use App\Models\PropertiesModel;
use App\Models\ClientModel;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // ✅ True full access only if ALL dashboard permissions are granted
        $fullAccess = $user->hasAllPermissions([
            'dashboard.sales_listings',
            'dashboard.sales',
            'dashboard.listings',
            'dashboard.executive'
        ]);

        $salesOnly    = $user->hasPermissionTo('dashboard.sales');
        $listingsOnly = $user->hasPermissionTo('dashboard.listings');
        $nicoleCustom = $user->hasPermissionTo('dashboard.executive');

        $agents = User::select('id', 'name')->get();

        // Mock data (you can leave as-is)
        $leads = Lead::select('year', 'month', 'location', 'source', 'count')
            ->orderBy('year')
            ->orderByRaw("FIELD(month, 'January','February','March','April','May','June','July','August','September','October','November','December')")
            ->get();

        $leadStats = Lead::selectRaw('year, month, COUNT(*) as total')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderByRaw("FIELD(month, 'January','February','March','April','May','June','July','August','September','October','November','December')")
            ->get();

       $salesStats = PropertiesModel::selectRaw("
                YEAR(created_at) AS year,
                MONTH(created_at) AS month_number,
                MONTHNAME(created_at) AS month_name,
                COUNT(*) AS total
            ")
            ->where('status', 'sold')
            ->groupByRaw("YEAR(created_at), MONTH(created_at), MONTHNAME(created_at)")
            ->orderByRaw("YEAR(created_at), MONTH(created_at)")
            ->get();



        $yearlyTarget     = 100;
        $quarterTarget    = 25;
        $salesThisYear    = $salesStats->sum('total');
        $salesThisQuarter = $salesStats->whereIn('month', ['April', 'May', 'June'])->sum('total');

        $allProperties    = PropertiesModel::all();
        $totalProperties = $allProperties->count();
        $activeListings = $totalProperties;


        $propertyTypes    = $allProperties->groupBy('type')->map->count();
        $propertyRegions  = $allProperties->groupBy('region')->map->count();
        $propertyStatuses = $allProperties->groupBy('status')->map->count();

        $deletedToday = PropertiesModel::onlyTrashed()->whereDate('deleted_at', today())->count();
        $listedToday  = PropertiesModel::whereDate('created_at', today())->count();

        $newClients = ClientModel::whereMonth('created_at', now()->month)->count();
        $clients = ClientModel::count();



        return view('dashboard.admin.dashboard', compact(
            'agents',
            'leads',
            'leadStats',
            'salesStats',
            'yearlyTarget',
            'quarterTarget',
            'salesThisYear',
            'salesThisQuarter',
            'allProperties',
            'propertyTypes',
            'propertyRegions',
            'propertyStatuses',
            'deletedToday',
            'listedToday',
            'fullAccess',
            'salesOnly',
            'listingsOnly',
            'nicoleCustom',
            'totalProperties',
            'activeListings',
            'newClients',
            'clients'

        ));
    }


    public function savePreferences(Request $request)
    {
        $user = auth()->user();

        // Save widget preferences as JSON in `dashboard_preferences` column
        $user->dashboard_preferences = json_encode($request->preferences ?? []);
        $user->save();

        return response()->json(['status' => 'success']);
    }

}
