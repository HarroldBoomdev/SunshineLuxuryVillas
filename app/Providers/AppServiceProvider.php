<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Models\AccessLog;
use App\Models\AgentsModel;
use App\Models\AuditLog;
use App\Models\ClientModel;
use App\Models\Deal;
use App\Models\DeveloperModel;
use App\Models\DiaryModel;
use App\Models\MatchModel;
use App\Models\PropertiesModel;
use App\Models\User;
use Illuminate\Support\Facades\Blade;
use App\View\Components\Dashboard\ReportWidget;
// use App\Observers\AccessLogObserver;
// use App\Observers\ModelObserver;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Register AccessLogObserver for tracking access-related events
        // AccessLog::observe(AccessLogObserver::class);

        // Register ModelObserver for CRUD operations (audit logs)
        // PropertiesModel::observe(ModelObserver::class);
        // ClientModel::observe(ModelObserver::class);
        // DeveloperModel::observe(ModelObserver::class);
        // DiaryModel::observe(ModelObserver::class);
        // MatchModel::observe(ModelObserver::class);
        // AgentsModel::observe(ModelObserver::class);
        // Deal::observe(ModelObserver::class);
        // User::observe(ModelObserver::class);
        // AuditLog::observe(ModelObserver::class);
        Blade::component('report-widget', ReportWidget::class);
    }
}
