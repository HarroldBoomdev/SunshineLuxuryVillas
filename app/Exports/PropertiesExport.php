<?php

namespace App\Exports;

use App\Models\PropertiesModel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PropertiesExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return PropertiesModel::all();
    }

    public function headings(): array
    {
        return \Schema::getColumnListing('properties');
    }
}
