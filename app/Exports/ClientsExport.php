<?php

namespace App\Exports;

use App\Models\ClientModel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ClientsExport implements FromCollection, WithHeadings
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = ClientModel::query();

        // Example: apply filters if needed
        if ($this->request->has('status')) {
            $query->where('Status', $this->request->status);
        }

        // Add more filters here as needed

        return $query->get();
    }

    public function headings(): array
    {
        return Schema::getColumnListing('clients');
    }
}
