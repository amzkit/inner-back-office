<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

use Storage;

// PhpOffice PhpSpreadsheet
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use App\Models\Sale;
use App\Models\Stall;

use Date;

class UploadController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            // Get store_id from VerifyStore middleware
            return $next($request);
        });
    }
    

    
    public function create(Request $request)
    {    

    }

    public function sales(Request $request)
    {
        //dd($request->all());
        $type = $request->type;
        $file = $request->file('file');
        $fileName = $file->getClientOriginalName();
        $fileExtension = $file->getClientOriginalExtension();        

        if($type === "xlsx"){
            $validator = \Validator::make(
                [
                    'file'      => $file,
                    'extension' => strtolower($fileExtension),
                ],
                [
                    'file'          => 'required',
                    'extension'      => 'required|in:xlsx,xls,csv,zip',
                ]
            );


            //$public_path = Util::getUserStoragePath('tracking_number/', true);
            $public_path = 'public/upload/';
            $storage_path = 'storage/upload/';
            //$file->storeAs($path, $fileName);
            $file = $file->storeAs($public_path, $fileName);
            //dd($path);
            if($fileExtension === 'zip'){
                $unzipper = new Unzip();
                //$fileName = $unzipper->extract(storage_path('app/'.$file), storage_path('app/public'))[0];
                //dd($public_path.$fileName, $public_path, $storage_path);
                $fileName = $unzipper->extract($storage_path.$fileName, $storage_path)[0];
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($storage_path.$fileName);
            }else{
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($storage_path.$fileName);
            }

            $sheetCount = $spreadsheet->getSheetCount(); 
            $sheetCount = 2;

            for ($j = 0; $j < $sheetCount; $j++) 
            {
                $worksheet = $spreadsheet->getSheet($j);
                
                $highestColumn = $worksheet->getHighestColumn(); // i.e. column : 'AE'
                $title_row = $worksheet->rangeToArray('A1:' . $highestColumn . '1')[0];
                $header_row = $worksheet->rangeToArray('A2:' . $highestColumn . '2')[0];
                //dd($header_row, $example_row);



                //dd($header_row, $column_names['stall_number'][0], array_search($column_names['stall_number'][0], $header_row));
                $column_names = [
                    'stall_number'  => ['摊位号'],
                    'owner'         => ['商家名称'],
                    'sale_total'    => ['总交易金额'],
                    'sale_count'    => ['笔数'],
                    'sale_date'     => ['清算日期'],
                    'transaction_fee' => ['手续费'],
                    'sale_fee'      => ['管理方入账金额'],
                    'thai_people'   => ['商户类型'],
                ];

                // File Type
                // Day sales vs Summary Sales
                $file_type_day = true;
                
                if(array_search($column_names['sale_count'][0], $header_row) > 0 && array_search($column_names['transaction_fee'][0], $header_row) > 0){
                    $file_type_day = true;
                }else{
                    $file_type_day = false;
                }

                if($file_type_day){
                    // File Type => Day sales

                    $default_index = [];

                    $maxCol = $worksheet->getHighestColumn();
                    $maxRow = $worksheet->getHighestRow(); // i.e. column : 'AE'
    
                    $index = [];
    
                    $index['stall_number']  =   array_search($column_names['stall_number'][0], $header_row)??2;
                    $index['owner']         =   array_search($column_names['owner'][0], $header_row)??1;
                    $index['sale_total']    =   array_search($column_names['sale_total'][0], $header_row)??4;
                    $index['sale_count']    =   array_search($column_names['sale_count'][0], $header_row)??5;
                    $index['sale_date']     =   array_search($column_names['sale_date'][0], $header_row)??6;
                    $index['transaction_fee']   =   array_search($column_names['transaction_fee'][0], $header_row)??7;
                    $index['sale_fee']      =   array_search($column_names['sale_fee'][0], $header_row)??8;
    
                    //dd($index['mobile_number']);
    
    
                    // Skip header row $i=2

                    for ($i = 2; $i <= 100; $i++) {

                        $row = $worksheet->rangeToArray('A' . $i . ':' . $maxCol . $i)[0];
                        //$order = Order::join('shipments', 'orders.id', '=', 'shipments.order_id')->first();
                        //dd($row);
                        $stall_number = trim($row[$index['stall_number']]);
                        if($stall_number && $stall_number[0] !== 'C'){
                            continue;
                        }
                        $sale_total = trim($row[$index['sale_total']]);
                        $sale_count = $row[$index['sale_count']];
                        $sale_date = $row[$index['sale_date']];
                        if(!$sale_date){
                            continue;
                        }
                        $transaction_fee = trim($row[$index['transaction_fee']]);
                        $sale_fee = trim($row[$index['sale_fee']]);
    
                        // Check first digit 0
                        $stall = Stall::where([['number', '=', $stall_number]])->first();
                        if(!$stall){
                            $stall = new Stall();
                            $stall->number = $stall_number;
                            $stall->save();
                        }
    
                        $sale = Sale::where([['sale_date', '=', $sale_date], ['stall_number', '=', $stall_number]])->first();
                        if(!$sale){
                            $sale = new Sale();
                            $sale->sale_date = $sale_date;
                            $sale->stall_number = $stall_number;
                            $sale->sale_total = str_replace(',', '', $sale_total);
                            $sale->sale_count = str_replace(',', '', $sale_count);
                            $sale->transaction_fee = str_replace(',', '', $transaction_fee);
                            $sale->sale_fee = str_replace(',', '', $sale_fee);
                            $sale->save();
                        }
    
                    }

                }else{
                    // File type summary sales

                    $maxCol = $worksheet->getHighestColumn();
                    $header_row = $worksheet->rangeToArray('A' . 2 . ':' . $maxCol . 2)[0];

                    $index = [];
                    $index['owner']         =   array_search($column_names['owner'][0], $header_row)??1;
                    $index['stall_number']  =   array_search($column_names['stall_number'][0], $header_row)??2;

                    $sale_date_header = ['','',''];

                    for($col = 3; $col < count($header_row); $col++){
                        // Check if header row (Date Row)
                        if(strpos($header_row[$col], '月') && strpos($header_row[$col], '日')){
                        //if(explode('月',$header_row[$col]) && explode('日',$header_row[$col])){
                            $day = date('Ymd', strtotime(date('Y').'-'.explode('月',$header_row[$col])[0].'-'.explode('日', explode('月',$header_row[$col])[1])[0]));
                            array_push($sale_date_header, $day);
                        }
                    }
                    
                    
                    //dd($sales);
                    //dd($header_row,$header_row[5],
                    //    date('Ymd', strtotime(date('Y').'-'.explode('月',$header_row[4])[0].'-'.explode('日', explode('月',$header_row[4])[1])[0])),
                    //);
                    $spreadsheet->setActiveSheetIndex($j);
                    $data = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(2,0)->getValue();
                    $db = $spreadsheet->getActiveSheet()->toArray();
                    //dd($sale_date_header);
                    for($row = 3; $row <= 100; $row++){
                        if(!isset($db[$row])){
                            continue;
                        }
                        $data = $db[$row];
//                        dd($data);

                        $stall_number = trim($data[$index['stall_number']]);
                        if($stall_number && $stall_number[0] !== 'C'){
                            // If not sales row
                            continue;
                        }
                        //dd($data);
                        for($col = 3; $col < count($data); $col++){
                            if(!isset($data[$col]) || $data[$col] == '' || $data[$col] == null)    continue;
                            //dd($sale_date_header[$col], $data[$col]);
                            $sale_date = $sale_date_header[$col];
                            $sale_total = $data[$col];
                            if($sale_total == null || $sale_total == ''){
                                // Fix bug some null, some empty string ''
                                $sale_total = 0.0;
                            }
                            $sale = Sale::where([['sale_date', '=', $sale_date], ['stall_number', '=', $stall_number]])->first();
                            if(!$sale){
                                $sale = new Sale();
                                $sale->sale_date = $sale_date;
                                $sale->stall_number = $stall_number;
                                $sale->sale_total = str_replace(',', '', $sale_total);
                                $sale->sale_count = str_replace(',', '', 0);
                                $sale->transaction_fee = str_replace(',', '', 0);
                                $sale->sale_fee = str_replace(',', '', 0);
                                $sale->save();
                            }
                        }
                    }
                }
                //dd($sale_date, $stall_number, $sale_total, $sale_count, $transaction_fee, $sale_fee);
            }

        }

        return response()->json(['success'=>true]);
            
        
    }


}
