<?php

namespace App\Http\Controllers;

use App\Models\PKBModels;
use Illuminate\Http\Request;
use App\Models\Transaction;
use Exception;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Ramsey\Uuid\Type\Integer;

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
        $errorPKB    = array();

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
            $getWO = PKBModels::select('*')->where('wo', '=', $wo)->where('customer', $customer)->get()->toArray();
            $idPKB = -1;

            if (!$getWO) {
                try {
                    $insert = PKBModels::updateOrCreate($PKB); # try insert until success
                    if ($insert) {
                        $idPKB        = $insert->id;
                    }
                } catch (Exception $error) { # if fails save exception
                    $errorPKB[]  = [
                        'wo'        => $wo,
                        'error'     => $error->getMessage()
                    ];
                    // continue;
                    dd($error);
                }
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
                'ppn'            => $ppn,
                'kode_ppn'       => $kodePPN,
                'total'          => $total,
                'kode_total'     => $kodeTotal,

                'id_pkb'         => $idPKB,
                'invoice_date'   => $invoiceDate,
            ];


            # check if data is balanced
            $totalNetDisc  = intval($discJasa) + intval($discParts) + intval($discBahan) + intval($discOPL) + intval($discOPB);
            $totalNetSales = intval($netJasa) + intval($netParts) + intval($netBahan) + intval($netOPL) + intval($netOPB);
            $balance       = $this->checkBalance($total, $totalNetDisc, $totalNetSales, $ppn);
            if ($balance) {
                try { # try insert
                    Transaction::updateOrCreate($detail);
                } catch (Exception $error) {
                    $errorPKB[]  = [
                        'wo'        => $wo,
                        'error'     => $error->getMessage()
                    ];
                    continue;
                }
            } else {
                # if data not balance then add array message to errorPKB
                $errorPKB[]   = [
                    'wo'      => $wo,
                    'error'   => "Hasil Penghitungan Tidak Balance"
                ];

                # delete PKB data
                PKBModels::where('wo', $wo)->where('customer', $customer)->delete();
                continue;
            }
        }

        //---- save error to a session
        session()->put('errorPKB', $errorPKB);
        if (!$errorPKB) {
            session()->flash('pesan', 'Selamat Data Berhasil Di Tambahkan');
        }
        return redirect()->to(session()->previousUrl());
    }

    protected function _exportPKB()
    {
        if (request()->has('id')) {
            if (request()->get('id')) {

                $id      = request()->get('id');
                $result  = PKBModels::select("*")
                    ->join('transaction_detail', 'transaction_pkb.id', '=', 'transaction_detail.id_pkb', 'left')
                    ->where('transaction_pkb.id', '=', $id)->get()->first()->toArray();

                $debitFields   = ['total', 'discJasa', 'discParts', 'discBahan', 'discOPL', 'discOPB'];
                $kreditFields  = ['jasa', 'parts', 'bahan', 'OPL', 'OPB', 'ppn'];
                $journalResult = array();
                foreach ($debitFields as $debit) {
                    $journalResult['debit'][] = [
                        'name'     => $debit,
                        'nominal'  => $result[$debit],
                    ];
                }

                foreach ($kreditFields as $kredit) {
                    $journalResult['kredit'][] = [
                        'name'     => $kredit,
                        'nominal'  => $result[$kredit],
                    ];
                }

                dd($journalResult);

                //---- Export to Excel
                $spreadsheet = new Spreadsheet();
                $spreadsheet->setActiveSheetIndex(0);

                //---- Create Header Information
                $spreadsheet->getActiveSheet()->setCellValue('A1', 'List Of')->getStyle('A1')
                    ->getFont()->setSize(16);
                $spreadsheet->getActiveSheet()->setCellValue('A2', 'Tanggal Cetak :')->getStyle('A2')
                    ->getFont()->setSize(10);
                $spreadsheet->getActiveSheet()->setCellValue('B2', date('d/m/Y H:i:s'))->getStyle('B2')
                    ->getFont()->setSize(10);
                $spreadsheet->getActiveSheet()->setCellValue('A3', 'Tahun Fiskal :')->getStyle('A3')
                    ->getFont()->setSize(10);
                $spreadsheet->getActiveSheet()->setCellValue('B3', $result[0]->tanggalInvoice . " - " . $result[sizeof($result) - 1]->tanggalInvoice)->getStyle('B3')
                    ->getFont()->setSize(10);
                $spreadsheet->getActiveSheet()->setCellValue('A4', 'Periode :')->getStyle('A4')
                    ->getFont()->setSize(10);
                $spreadsheet->getActiveSheet()->setCellValue('B4', $result[0]->tanggalInvoice . " - " . $result[sizeof($result) - 1]->tanggalInvoice)->getStyle('B4')
                    ->getFont()->setSize(10);
                $spreadsheet->getActiveSheet()->setCellValue('A5', 'Jenis :')->getStyle('A5')
                    ->getFont()->setSize(10);
                $spreadsheet->getActiveSheet()->setCellValue('B5', 'Seluruhnya')->getStyle('B5')
                    ->getFont()->setSize(10);

                //---- User Account
                $spreadsheet->getActiveSheet()->setCellValue('E3', 'jurnal.akastra.id')->getStyle('E3')
                    ->getFont()->setSize(10);
                $spreadsheet->getActiveSheet()->setCellValue('E4', 'ADH')->getStyle('E4')
                    ->getFont()->setSize(10);


                //---- Create Table Header
                $tableHeader  = ['Tipe/Akun', 'Referensi/Nama Akun', 'Tgl/Dim', 'Orang/Barang/Memo', 'Debit', 'Kredit', 'Kredit Name'];
                $columnArray  = range("A", "G");
                foreach ($columnArray as $index => $column) {
                    $spreadsheet->getActiveSheet()->setCellValue($column . '6', $tableHeader[$index])->getStyle($column . '6')
                        ->getFont()->setSize(10)->setItalic(true);
                    $spreadsheet->getActiveSheet()->getCell($column . "6")->getStyle()->getAlignment()->setVertical('center')->setHorizontal('center');
                }
                $spreadsheet->getActiveSheet()->getRowDimension(6)->setRowHeight(26);

                // //---- Create Table
                // $row = 7; //---- Row Start From
                // foreach ($result as $index => $key) {

                //     if ($index === 0) {
                //         $spreadsheet->getActiveSheet()->setCellValue('C' . $row, $key->tanggalInvoice)->getStyle($column . '6')
                //             ->getFont()->setSize(10)->setItalic(true);
                //         $spreadsheet->getActiveSheet()->setCellValue('B' . $row, $key->WoNo)->getStyle($column . '6')
                //             ->getFont()->setSize(10)->setItalic(true);
                //         $row++;
                //     }

                //     if ($key->kredit > 0) {
                //         $spreadsheet->getActiveSheet()->setCellValue('A' . $row, $key->kodeAkun)->getStyle($column . '6')
                //             ->getFont()->setSize(10)->setItalic(true);
                //         $spreadsheet->getActiveSheet()->setCellValue('D' . $row, $key->deskripsi)->getStyle($column . '6')
                //             ->getFont()->setSize(10)->setItalic(true);
                //         $spreadsheet->getActiveSheet()->setCellValue('E' . $row, number_format($key->debit, 0, ',', '.'))->getStyle($column . '6')
                //             ->getFont()->setSize(10)->setItalic(true);

                //         $spreadsheet->getActiveSheet()->setCellValue('F' . $row, number_format($key->kredit, 0, ',', '.'))->getStyle($column . '6')
                //             ->getFont()->setSize(10)->setItalic(true);
                //         $spreadsheet->getActiveSheet()->setCellValue('G' . $row, strtoupper($key->kreditName))->getStyle($column . '6')
                //             ->getFont()->setSize(10)->setItalic(true);
                //         $row++;
                //     }
                // }


                //--- Set Auto Width
                foreach (range("A", "G") as $columnID) {
                    $spreadsheet->getActiveSheet()->getColumnDimension($columnID)
                        ->setAutoSize(true);
                }

                $writer   = new Xlsx($spreadsheet);
                $filename = "Jurnal ";


                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename=' . $filename . '.xlsx');
                header('Cache-Control: max-age=0');
                $writer->save('php://output');
                die;
            }
        }
    }

    protected function _exportAll()
    {
        if (request()->has('startDate') && request()->has('endDate')) {
            if (request()->get('startDate') && request()->get('endDate')) {

                $startDate = request()->get('startDate');
                $endDate   = request()->get('endDate');

                //----- Result Query Convert to Jurnal
                $firstQuery = DB::table('transaction_pkb')->select(DB::raw("kode_jasa as kodeAkun,
                invoice_date as tanggalInvoice,
                CONCAT(customer, ' | ', license_plate) as deskripsi,
                wo as WoNo,
                SUM(total) as debit,
                '' as kreditName,
                0 as kredit"))->join('transaction_detail', 'transaction_pkb.id', '=', 'transaction_detail.id_pkb', 'left')
                    ->whereDate('transaction_detail.invoice_date', '>=', $startDate)->where('transaction_detail.invoice_date', '<=', $endDate)->groupBy('transaction_detail.wo');

                $jasa = DB::table('transaction_pkb')->select(DB::raw("kode_jasa as kodeAkun,
                invoice_date as tanggalInvoice,
                CONCAT(customer, ' | ', license_plate) as deskripsi,
                wo as WoNo,
                SUM(total) as debit,
                'jasa' as kreditName,
                jasa as kredit"))->join('transaction_detail', 'transaction_pkb.id', '=', 'transaction_detail.id_pkb', 'left')
                    ->whereDate('transaction_detail.invoice_date', '>=', $startDate)->where('transaction_detail.invoice_date', '<=', $endDate)->groupBy('transaction_detail.wo')
                    ->unionAll($firstQuery);

                $parts = DB::table('transaction_pkb')->select(DB::raw("kode_parts as kodeAkun,
                    invoice_date as tanggalInvoice,
                    CONCAT(customer, ' | ', license_plate) as deskripsi,
                    wo as WoNo,
                    SUM(total) as debit,
                    'parts' as kreditName,
                    parts as kredit"))->join('transaction_detail', 'transaction_pkb.id', '=', 'transaction_detail.id_pkb', 'left')
                    ->whereDate('transaction_detail.invoice_date', '>=', $startDate)->where('transaction_detail.invoice_date', '<=', $endDate)->groupBy('transaction_detail.wo')
                    ->unionAll($jasa);

                $bahan = DB::table('transaction_pkb')->select(DB::raw("kode_bahan as kodeAkun,
                    invoice_date as tanggalInvoice,
                    CONCAT(customer, ' | ', license_plate) as deskripsi,
                    wo as WoNo,
                    SUM(total) as debit,
                    'bahan' as kreditName,
                    bahan as kredit"))->join('transaction_detail', 'transaction_pkb.id', '=', 'transaction_detail.id_pkb', 'left')
                    ->whereDate('transaction_detail.invoice_date', '>=', $startDate)->where('transaction_detail.invoice_date', '<=', $endDate)->groupBy('transaction_detail.wo')
                    ->unionAll($parts);

                $opl = DB::table('transaction_pkb')->select(DB::raw("kode_opl as kodeAkun,
                    invoice_date as tanggalInvoice,
                    CONCAT(customer, ' | ', license_plate) as deskripsi,
                    wo as WoNo,
                    SUM(total) as debit,
                    'opl' as kreditName,
                    OPL as kredit"))->join('transaction_detail', 'transaction_pkb.id', '=', 'transaction_detail.id_pkb', 'left')
                    ->whereDate('transaction_detail.invoice_date', '>=', $startDate)->where('transaction_detail.invoice_date', '<=', $endDate)->groupBy('transaction_detail.wo')
                    ->unionAll($bahan);

                //--- as OPB
                $result = DB::table('transaction_pkb')->select(DB::raw("kode_opb as kodeAkun,
                    invoice_date as tanggalInvoice,
                    CONCAT(customer, ' | ', license_plate) as deskripsi,
                    wo as WoNo,
                    SUM(total) as debit,
                    'opb' as kreditName,
                    OPB as kredit"))->join('transaction_detail', 'transaction_pkb.id', '=', 'transaction_detail.id_pkb', 'left')
                    ->whereDate('transaction_detail.invoice_date', '>=', $startDate)->where('transaction_detail.invoice_date', '<=', $endDate)->groupBy('transaction_detail.wo')
                    ->unionAll($opl)->orderBy('debit', 'DESC')->get()->toArray();


                if (!$result) {
                    session()->flash('error', 'Jurnal tidak di temukan di tanggal tersebut');
                    return redirect()->to(session()->previousUrl());
                }

                $uniqueWO = DB::table('transaction_pkb')->select('wo')->distinct()->get()->toArray();
                $resultGroup = []; //---- Grouping Wrapper

                //---- Grouping PKB with the same Work Order
                foreach ($uniqueWO as $grup) {
                    $keys = array_keys(array_combine(array_keys($result), array_column($result, 'WoNo')), $grup->wo);
                    foreach ($keys as $data) {
                        $resultGroup[$grup->wo][] = $result[$data];
                    }
                }

                //---- Export to Excel
                $spreadsheet = new Spreadsheet();
                $spreadsheet->setActiveSheetIndex(0);

                //---- Create Header Information
                $spreadsheet->getActiveSheet()->setCellValue('A1', 'List Of')->getStyle('A1')
                    ->getFont()->setSize(16);
                $spreadsheet->getActiveSheet()->setCellValue('A2', 'Tanggal Cetak :')->getStyle('A2')
                    ->getFont()->setSize(10);
                $spreadsheet->getActiveSheet()->setCellValue('B2', date('d/m/Y H:i:s'))->getStyle('B2')
                    ->getFont()->setSize(10);
                $spreadsheet->getActiveSheet()->setCellValue('A3', 'Tahun Fiskal :')->getStyle('A3')
                    ->getFont()->setSize(10);
                $spreadsheet->getActiveSheet()->setCellValue('B3', $result[0]->tanggalInvoice . " - " . $result[sizeof($result) - 1]->tanggalInvoice)->getStyle('B3')
                    ->getFont()->setSize(10);
                $spreadsheet->getActiveSheet()->setCellValue('A4', 'Periode :')->getStyle('A4')
                    ->getFont()->setSize(10);
                $spreadsheet->getActiveSheet()->setCellValue('B4', $result[0]->tanggalInvoice . " - " . $result[sizeof($result) - 1]->tanggalInvoice)->getStyle('B4')
                    ->getFont()->setSize(10);
                $spreadsheet->getActiveSheet()->setCellValue('A5', 'Jenis :')->getStyle('A5')
                    ->getFont()->setSize(10);
                $spreadsheet->getActiveSheet()->setCellValue('B5', 'Seluruhnya')->getStyle('B5')
                    ->getFont()->setSize(10);

                //---- User Account
                $spreadsheet->getActiveSheet()->setCellValue('E3', 'jurnal.akastra.id')->getStyle('E3')
                    ->getFont()->setSize(10);
                $spreadsheet->getActiveSheet()->setCellValue('E4', 'ADH')->getStyle('E4')
                    ->getFont()->setSize(10);


                //---- Create Table Header
                $tableHeader  = ['Tipe/Akun', 'Referensi/Nama Akun', 'Tgl/Dim', 'Orang/Barang/Memo', 'Debit', 'Kredit', 'Kredit Name'];
                $columnArray  = range("A", "G");
                foreach ($columnArray as $index => $column) {
                    $spreadsheet->getActiveSheet()->setCellValue($column . '6', $tableHeader[$index])->getStyle($column . '6')
                        ->getFont()->setSize(10)->setItalic(true);
                    $spreadsheet->getActiveSheet()->getCell($column . "6")->getStyle()->getAlignment()->setVertical('center')->setHorizontal('center');
                }
                $spreadsheet->getActiveSheet()->getRowDimension(6)->setRowHeight(26);

                //---- Create Table
                $row = 7; //---- Row Start From
                if ($resultGroup) {
                    foreach ($resultGroup as $NoWo) {
                        foreach ($NoWo as $index => $key) {

                            if ($index === 0) {
                                $spreadsheet->getActiveSheet()->setCellValue('C' . $row, $key->tanggalInvoice)->getStyle($column . '6')
                                    ->getFont()->setSize(10)->setItalic(true);
                                $spreadsheet->getActiveSheet()->setCellValue('B' . $row, $key->WoNo)->getStyle($column . '6')
                                    ->getFont()->setSize(10)->setItalic(true);
                                $row++;
                            }

                            if ($key->kredit > 0) {
                                $spreadsheet->getActiveSheet()->setCellValue('A' . $row, $key->kodeAkun)->getStyle($column . '6')
                                    ->getFont()->setSize(10)->setItalic(true);
                                $spreadsheet->getActiveSheet()->setCellValue('D' . $row, $key->deskripsi)->getStyle($column . '6')
                                    ->getFont()->setSize(10)->setItalic(true);
                                $spreadsheet->getActiveSheet()->setCellValue('E' . $row, number_format($key->debit, 0, '.', '.'))->getStyle($column . '6')
                                    ->getFont()->setSize(10)->setItalic(true);

                                $spreadsheet->getActiveSheet()->setCellValue('F' . $row, number_format($key->kredit, 0, '.', '.'))->getStyle($column . '6')
                                    ->getFont()->setSize(10)->setItalic(true);
                                $spreadsheet->getActiveSheet()->setCellValue('G' . $row, strtoupper($key->kreditName))->getStyle($column . '6')
                                    ->getFont()->setSize(10)->setItalic(true);
                                $row++;
                            }
                        }
                    }
                }


                //--- Set Auto Width
                foreach ($columnArray as $columnID) {
                    $spreadsheet->getActiveSheet()->getColumnDimension($columnID)
                        ->setAutoSize(true);
                }

                $writer   = new Xlsx($spreadsheet);
                $filename = "Jurnal " . "$startDate - $endDate";


                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename=' . $filename . '.xlsx');
                header('Cache-Control: max-age=0');
                $writer->save('php://output');
                die;
            }
        }
    }

    protected function checkBalance(int $revenue, int $totalDisc, int $totalSales, int $PPN)
    {
        $result = false;
        $verify = ($revenue + $totalDisc) - ($totalSales + $PPN);
        if ($verify == 0) {
            $result = true;
        } else {
            $result = false;
        }
        return $result;
    }
}
