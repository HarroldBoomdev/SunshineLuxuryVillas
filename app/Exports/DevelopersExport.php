<?php

namespace App\Exports;

use App\Models\DeveloperModel;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Exports\DevelopersExport;
use Maatwebsite\Excel\Facades\Excel;

class DevelopersExport implements FromCollection, WithHeadings
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = DeveloperModel::query();

        if (!empty($this->filters['reference'])) {
            $query->where('reference', 'like', '%' . $this->filters['reference'] . '%');
        }

        if (!empty($this->filters['name'])) {
            $query->where(function ($q) {
                $name = $this->filters['name'];
                $q->where('first_name', 'like', '%' . $name . '%')
                  ->orWhere('last_name', 'like', '%' . $name . '%');
            });
        }

        if (!empty($this->filters['email'])) {
            $query->where('email', 'like', '%' . $this->filters['email'] . '%');
        }

        return $query->get();
    }

    public function headings(): array
    {
        return Schema::getColumnListing((new DeveloperModel)->getTable());
    }
}
