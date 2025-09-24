<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\BankProperty;
use Illuminate\Http\Request;

class BankController extends Controller
{
    public function index(Request $request)
    {
        $bankId = $request->integer('bank_id');

        // Always provide dropdown options
        $bankOptions = \App\Models\Bank::orderBy('name')
            ->get(['id','name']);

        if (!$bankId) {
            // MODE A: default list of all banks
            $banks = Bank::orderBy('name')
                ->paginate(20, ['id','reference','address','name','telephone','mobile']);

            return view('banks.index', [
                'mode'        => 'all',
                'bankOptions' => $bankOptions,
                'banks'       => $banks,
                'selectedBank'=> null,
                'links'       => null,
            ]);
        }

        // MODE B: one bank selected → details + linked properties
        $selectedBank = Bank::find($bankId, ['id','reference','address','name','telephone','mobile']);

        $links = BankProperty::where('bank_id', $bankId)
            ->orderBy('property_reference')
            ->paginate(20);

        return view('banks.index', [
            'mode'        => 'one',
            'bankOptions' => $bankOptions,
            'banks'       => null,
            'selectedBank'=> $selectedBank,
            'links'       => $links,
        ]);
    }
}
