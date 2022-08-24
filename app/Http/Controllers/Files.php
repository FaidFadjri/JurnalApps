<?php

namespace App\Http\Controllers;

use App\Models\KreditModels;
use App\Models\PKBModels;
use Illuminate\Http\Request;

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

            $PKB = [
                'wo'            => $wo,
                'invoice_date'  => date('Y-m-d', strtotime($data[$row][2])),
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

            $kredit = [
                'jasa'       => $netJasa,
                'parts'      => $netParts,
                'bahan'      => $netBahan,
                'OPL'        => $netOPL,
                'OPB'        => $netOPB,
                'kode_jasa'  => $kodeJasa,
                'kode_parts' => $kodePart,
                'kode_bahan' => $kodeBahan,
                'kode_opl'   => $kodeOPL,
                'kode_opb'   => $kodeOPB,
                'wo'         => $data[$row][1]
            ];

            KreditModels::updateOrCreate($kredit);
        }
    }
}
