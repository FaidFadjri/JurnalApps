<?php

namespace App\Http\Controllers;

use App\Models\PKBModels;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Yajra\DataTables\DataTables;

class Operation extends Controller
{
    protected function _loadTransaksi()
    {

        $data = DB::table('transaction_pkb')->select(DB::raw('*, jasa as jasa'))
            ->join('transaction_detail', 'transaction_pkb.id', '=', 'transaction_detail.id_pkb');


        #---- check if start date and end date was exist
        if (request()->has('startDate')) {
            $startDate = request()->get('startDate'); #---- save start date in variable
            if ($startDate) { #----- if start date was not null
                $data->whereDate('invoice_date', '>=', $startDate);
            }
        }

        if (request()->has('endDate')) {
            $endDate  = request()->get('endDate'); #---- save end date in variable
            if ($endDate) { #---- if end date was not null
                $data->whereDate('invoice_date', '<=', $endDate);
            }
        }

        return Datatables::of($data->get()->toArray())->addColumn('action', function ($data) {
            $html  = '';
            $html .= '<form action="/export/wo" method="POST">';
            $html .= '<input type="hidden" name="_token" value=" ' . csrf_token() . ' ">';
            $html .= '<input type="hidden" name="id" value="' . $data->id_pkb . '">';
            $html .= '<a class="btn-delete btn btn-danger px-3 py-2" data-id="' . $data->id_pkb . '">Hapus</a>';
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
        if (request()->has('id')) {
            if (request()->get('id')) {
                $id            = request()->get('id');
                $PKB           = PKBModels::select('*')->where('id', $id)->get()->toArray();
                if (!$PKB) {
                    return response()->json('Error Code : Delete PKB x 404', 404);
                } else {
                    $delete   = PKBModels::find($id)->delete();
                    if ($delete) {
                        return response()->json("Penghapusan Data Berhasil", 200);
                    } else {
                        return response()->json("Internal server error", 500);
                    }
                }
            }
        }
    }



    //---- Handle Error
    protected function _deleteError()
    {
        session()->forget('errorPKB');
        return redirect()->to(session()->previousUrl());
    }

    protected function _exportError()
    {
        //---- Export to Excel
        $spreadsheet = new Spreadsheet();
        $spreadsheet->setActiveSheetIndex(0);

        //---- Create Header Information
        $spreadsheet->getActiveSheet()->setCellValue('A1', 'Daftar PKB Error')->getStyle('A1')
            ->getFont()->setSize(16);
        $spreadsheet->getActiveSheet()->setCellValue('A2', "Tanggal Cetak : " . date("Y/m/d"))->getStyle('A2')
            ->getFont()->setSize(10);
        $spreadsheet->getActiveSheet()->setCellValue('A4', "NOMOR WO")->getStyle('A4')
            ->getFont()->setSize(10)->setItalic(true);
        $spreadsheet->getActiveSheet()->setCellValue('B4', "ERROR")->getStyle('B4')
            ->getFont()->setSize(10)->setItalic(true);

        $writer   = new Xlsx($spreadsheet);
        $filename = "IMPORT-ERROR-WO- " . date('Y-m-d');

        $error    = session()->get('errorPKB');

        $row      = 5; # row start
        foreach ($error as $index => $item) {
            $spreadsheet->getActiveSheet()->setCellValue("A" . $row, $item['wo'])->getStyle('A' . $row)
                ->getFont()->setSize(10);
            $spreadsheet->getActiveSheet()->setCellValue("B" . $row, $item['error'])->getStyle('B' . $row)
                ->getFont()->setSize(10);
            $row++;
        }

        //--- Set Auto Width
        foreach (range("A", "E") as $columnID) {
            $spreadsheet->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename . '.xlsx');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        die;
    }
}
