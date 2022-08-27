<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class Operation extends Controller
{
    protected function _loadTransaksi()
    {

        $data = DB::table('transaction_pkb')->select(DB::raw('*, jasa as jasa'))
            ->join('transaction_detail', 'transaction_pkb.wo', '=', 'transaction_detail.wo')->get()->toArray();
        return Datatables::of($data)->addColumn('action', function ($data) {
            $html  = '';
            $html .= '<form action="/export/wo" method="POST">';
            $html .= '<input type="hidden" name="_token" value="Ga4ZQZAJCk0YlsJIvSU9BOtaYZKkjhK2VpB9Epx0">';
            $html .= '<input type="hidden" name="wo" value="' . $data->wo . '">';
            $html .= '<a class="btn-delete btn btn-danger px-3 py-2" data-wo="' . $data->wo . '">Hapus</a>';
            $html .= '<button type="submit" class="btn-detail btn btn-success px-3 py-2 ml-1">Export Jurnal</button>';
            $html .= '</form>';
            return $html;
        })->addIndexColumn()->make(true);
    }

    protected function _detailTransaksi()
    {
        if (request()->has('wo')) {
            $NoWo    = request()->get('wo');
            $fields  = 'transaction_pkb.wo, license_plate, customer,';
            $fields .= 'transaction_kredit.jasa, transaction_kredit.parts, transaction_kredit.bahan, transaction_kredit.OPL, transaction_kredit.OPB,';
            $fields .= 'transaction_debit.jasa as discJasa, transaction_debit.parts as discParts, transaction_debit.bahan as discBahan, transaction_debit.OPL as discOPL, transaction_debit.OPB as discOPB';

            $detail = DB::table('transaction_pkb')->select(DB::raw($fields))
                ->join('transaction_debit', 'transaction_pkb.wo', '=', 'transaction_debit.wo')
                ->join('transaction_kredit', 'transaction_pkb.wo', '=', 'transaction_kredit.wo')
                ->where('transaction_pkb.wo', $NoWo)->get()->toArray();

            return response()->json($detail, 200);
        }
    }

    protected function _searchPKB()
    {
        if (request()->has('keyword')) {
            if (request()->get('keyword')) {
                $keyword = request()->get('keyword');
                $result  = DB::table('transaction_pkb')->select('*')->where('customer', 'like', "%$keyword%")
                    ->limit(3)->get()->toArray();
                if ($result) {
                    $response = [
                        'message' => 'Berhasil mendapatkan data',
                        'result'  => $result
                    ];
                    return response()->json($response, 200);
                } else {
                    $response = [
                        'message' => 'Data berhasil',
                    ];
                    return response()->json($response, 404);
                }
            }
        }
    }

    protected function _deletePKB()
    {
        if (request()->has('wo')) {
            if (request()->get('wo')) {
                $wo            = request()->get('wo');
                $deleteDetail  = DB::table('transaction_detail')->where('wo', $wo)->delete();
                if ($deleteDetail) {
                    $deletePKB = DB::table('transaction_pkb')->where('wo', $wo)->delete();
                    if ($deletePKB) {
                        return response()->json($wo, 200);
                    } else {
                        return response()->json('Error Code : Delete x WO', 500);
                    }
                } else {
                    return response()->json('Error Code : Delete x WO Detail', 500);
                }
            }
        }
    }
}
