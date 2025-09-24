<?php

namespace App\View\Components\Dashboard;

use Illuminate\View\Component;

class ReportWidget extends Component
{
    public $title;
    public $value;

    public function __construct($title, $value)
    {
        $this->title = $title;
        $this->value = $value;
    }

    public function render()
    {
        return view('components.dashboard.report-widget');
    }
}
