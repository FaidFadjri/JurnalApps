<?php

namespace App\Http\Controllers;

use App\Models\PKBModels;
use Illuminate\Http\Request;
use App\Models\Transaction;

class Files extends Controller
{
    protected function _import()
    {
        $file = request()->file('file');
        $ext  = $file->extension();
        if ($ext == 'xls') {
            $render = new \PhpOffice\PhpSpreadsheet\Reader\Xls;
        } else if ($ext == 'xlsx') {
            $render = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx;
        } else {
            return redirect()->to('import');
        }

        $spreadsheet = $render->load($file);
        $data        = $spreadsheet->getActiveSheet()->toArray();

        //---- Data start from index 2 which used as row in excel
        //---- Index 1 and 2 was the heading
        for ($row = 2; $row < sizeof($data); $row++) {

            # insert work order if unique
            $wo            = $data[$row][1];
            $license_plate = $data[$row][3];
            $customer      = $data[$row][4];
            $invoiceDate   = date('Y-m-d', strtotime($data[$row][2]));

            $PKB = [
                'wo'            => $wo,
                'license_plate' => $license_plate,
                'customer'      => $customer
            ];
            $getWO = PKBModels::select('*')->where('wo', '=', $wo)->get()->toArray();
            if (!$getWO) {
                PKBModels::updateOrCreate($PKB); # if there is no work order then insert it
            }

            # insert kredit
            $netJasa  = $data[$row][6];
            $netParts = $data[$row][9];
            $netBahan = $data[$row][12];
            $netOPL   = $data[$row][15];
            $netOPB   = $data[$row][18];

            $kodeJasa  = $data[$row][7];
            $kodePart  = $data[$row][10];
            $kodeBahan = $data[$row][13];
            $kodeOPL   = $data[$row][16];
            $kodeOPB   = $data[$row][19];

            # Insert Debit
            $discJasa  = $data[$row][21];
            $discParts = $data[$row][24];
            $discBahan = $data[$row][27];
            $discOPL   = $data[$row][30];
            $discOPB   = $data[$row][33];
            $ppn       = $data[$row][36];
            $total     = $data[$row][38];

            $kodeDiscJasa  = $data[$row][22];
            $kodeDiscPart  = $data[$row][25];
            $kodeDiscBahan = $data[$row][28];
            $kodeDiscOPL   = $data[$row][31];
            $kodeDiscOPB   = $data[$row][34];
            $kodePPN       = $data[$row][37];
            $kodeTotal     = $data[$row][39];

            $detail = [
                'jasa'         => $netJasa,
                'parts'        => $netParts,
                'bahan'        => $netBahan,
                'OPL'          => $netOPL,
                'OPB'          => $netOPB,
                'kode_jasa'    => $kodeJasa,
                'kode_parts'   => $kodePart,
                'kode_bahan'   => $kodeBahan,
                'kode_opl'     => $kodeOPL,
                'kode_opb'     => $kodeOPB,

                'discJasa'       => $discJasa,
                'discParts'      => $discParts,
                'discBahan'      => $discBahan,
                'discOPL'        => $discOPL,
                'discOPB'        => $discOPB,
                'kode_discJasa'  => $kodeDiscJasa,
                'kode_discParts' => $kodeDiscPart,
                'kode_discBahan' => $kodeDiscBahan,
                'kode_discOpl'   => $kodeDiscOPL,
                'kode_discOpb'   => $kodeDiscOPB,
                'ppn'        => $ppn,
                'kode_ppn'   => $kodePPN,
                'total'      => $total,
                'kode_total' => $kodeTotal,

                'wo'           => $wo,
                'invoice_date' => $invoiceDate,
            ];

            Transaction::updateOrCreate($detail);




            // $debit = [
            //     'jasa'       => $discJasa,
            //     'parts'      => $discParts,
            //     'bahan'      => $discBahan,
            //     'OPL'        => $discOPL,
            //     'OPB'        => $discOPB,
            //     'kode_jasa'  => $kodeDiscJasa,
            //     'kode_parts' => $kodeDiscPart,
            //     'kode_bahan' => $kodeDiscBahan,
            //     'kode_opl'   => $kodeDiscOPL,
            //     'kode_opb'   => $kodeDiscOPB,
            //     'ppn'        => $ppn,
            //     'kode_ppn'   => $kodePPN,
            //     'total'      => $total,
            //     'kode_total' => $kodeTotal,
            //     'wo'         => $wo,
            //     'invoice_date' => $invoiceDate
            // ];
        }
        session()->flash('pesan', 'Data penjualan berhasil di upload');
        return redirect()->to(session()->previousUrl());
    }
}
