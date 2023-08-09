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
use App\Models\Team;
use App\Models\TeamStall;
use DateTime;
use DatePeriod;
use DateInterval;
use stdClass;

class DataController extends Controller
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

    public function stalls(Request $request)
    {
        $stalls = Stall::orderBy('number')->get();

        $stall_list = array();
        foreach($stalls as $i=>$stall){
            $temp = $stall->number;
            array_push($stall_list, $temp);
        }

        return response()->json(['success'=>true, 'stall_list'=>$stall_list]);
    }

    public function teams(Request $request)
    {
        $teams = Team::all();

        $team_list = array();
        foreach($teams as $i=>$team){
            $temp = ['value'=>$team->id, 'title'=>$team->name];
            array_push($team_list, $temp);
        }

        return response()->json(['success'=>true, 'team_list'=>$team_list]);
    }

    public function stall_sales(Request $request)
    {

        $stall_number = $request->stall_number;
        //dd($request->all(), $stall_number);
        //$stall = Stall::where([['number'=>$stall_number]])->first();

        $stall_sale_list = array();
        $sales = Sale::where([['stall_number', '=', $stall_number]])->orderBy('sale_date', 'DESC')->get();

        //dd($sales);
        $total_sum = 0;
        $count_sum = 0;
        $week_sum = 0;

        $week_count = 0;

        foreach($sales as $i=>$sale){
            $temp = ['sale_date'=>$sale->sale_date, 'sale_total'=>$sale->sale_total, 'sale_count'=>$sale->sale_count];
            $total_sum += $sale->sale_total;
            $count_sum += $sale->sale_count;
            
            array_push($stall_sale_list, $temp);

            $day_of_week = date('w', strtotime($sale->sale_date));
            $temp['day_of_week'] = $day_of_week;
            $temp['prv_of_week'] = $previous_day_of_week??6;
            
            if($i == 0) $previous_day_of_week = $day_of_week;
            if($day_of_week <= $previous_day_of_week){
                $week_sum += $sale->sale_total;
                $week_count += $sale->sale_count;
                if($i+1 == count($sales)){
                    array_push($stall_sale_list, ['sale_date'=>'week sum', 'sale_total'=>$week_sum, 'sale_count'=>$week_count]);
                }
            }else{
                $week_sum += $sale->sale_total;
                $week_count += $sale->sale_count;
                array_push($stall_sale_list, ['sale_date'=>'week sum', 'sale_total'=>$week_sum, 'sale_count'=>$week_count]);
                $week_sum = 0;
                $week_count = 0;
            }
            $previous_day_of_week = $day_of_week;
        }
        array_push($stall_sale_list, ['sale_date'=>'รวม', 'sale_total'=>$total_sum, 'sale_count'=>$count_sum]);

        return response()->json(['success'=>true, 'stall_sale_list'=>$stall_sale_list]);

    }

    public function team_sales(Request $request)
    {
        $team_id = $request->team;
        $team = Team::find($team_id);

        $team_stalls = TeamStall::where([['team_id','=', $team_id]])->get();
        $team_sale_list = [];
        $team_sale_total_sum = 0;
        $team_sale_count_sum = 0;

        $from = date('20230801');
        $to = date('Ymd');

        $dates = new DatePeriod(new DateTime($from), new DateInterval('P1D'), new DateTime($to));

        $team_sale_list = array();

        $stall_list = [];
        foreach($team_stalls as $stall){
            array_push($stall_list, $stall->stall_number);
        }
        array_push($stall_list, 'รวม');
        $team_sale_list['header'] = $stall_list;

        $stall_sales_by_date = array();
        $stall_sum = array();
        $team_sum = 0;
        foreach($dates as $i=>$date){
            $temp = [];
            $sale_date_sum = 0.0;
            foreach($team_stalls as $j=>$stall){
                $sale = Sale::where([
                    ['stall_number', '=', $stall->stall_number],
                    ['sale_date','=', $date]
                    ])->first();
                array_push($temp, $sale->sale_total??0.0);
                if(!array_key_exists($j, $stall_sum)){
                    //$stall_sum[$stall->stall_number] = 0;
                    $stall_sum[$j] = 0;

                }
                //$stall_sum[$stall->stall_number] += $sale->sale_total??0.0;
                $stall_sum[$j] += $sale->sale_total??0.0;

                $sale_date_sum += $sale->sale_total??0.0;
            }
            array_push($temp, $sale_date_sum);
            $stall_sales_by_date[$date->format('d M')] = $temp;
        }
        //dd($sales_by_date);
        $stall_sum[count($stall_list)] = array_sum($stall_sum);

        $stall_sales_by_date['รวม'] = $stall_sum;
        $team_sale_list['sales_by_date'] = $stall_sales_by_date;


        /*
        foreach($team_stalls as $i=>$stall){

            $stall_number = $stall->stall_number;
            
            //dd($request->all(), $stall_number);
            //$stall = Stall::where([['number'=>$stall_number]])->first();
    
            $stall_sale_list = array();

            $sales = Sale::where([['stall_number', '=', $stall_number]])
                ->orderBy('sale_date', 'DESC')
                ->whereBetween('sale_date', [$from, $to])
                ->get();
    
            //dd($sales);
            $total_sum = 0;
            $count_sum = 0;
            $week_sum = 0;
    
            $week_count = 0;
    
            foreach($sales as $j=>$sale){
                $temp = ['sale_date'=>$sale->sale_date, 'sale_total'=>$sale->sale_total, 'sale_count'=>$sale->sale_count];
                $total_sum += $sale->sale_total;
                $count_sum += $sale->sale_count;
                
                array_push($stall_sale_list, $temp);
    
                $day_of_week = date('w', strtotime($sale->sale_date));
                $temp['day_of_week'] = $day_of_week;
                $temp['prv_of_week'] = $previous_day_of_week??6;
                
                if($j == 0) $previous_day_of_week = $day_of_week;
                if($day_of_week <= $previous_day_of_week){
                    $week_sum += $sale->sale_total;
                    $week_count += $sale->sale_count;
                    if($j+1 == count($sales)){
                        //array_push($stall_sale_list, ['sale_date'=>'week sum', 'sale_total'=>$week_sum, 'sale_count'=>$week_count]);
                    }
                }else{
                    $week_sum += $sale->sale_total;
                    $week_count += $sale->sale_count;
                    //array_push($stall_sale_list, ['sale_date'=>'week sum', 'sale_total'=>$week_sum, 'sale_count'=>$week_count]);
                    $week_sum = 0;
                    $week_count = 0;
                }
                $previous_day_of_week = $day_of_week;
            }
            array_push($stall_sale_list, ['sale_date'=>'รวม', 'sale_total'=>$total_sum, 'sale_count'=>$count_sum]);
            $team_sale_total_sum += $total_sum;
            $team_sale_count_sum += $count_sum;
            array_push($team_sale_list, ['stall_number'=>$stall_number, 'sales'=>$stall_sale_list]);
        }
        $team = $team->toArray();
        */
        //$team['team_sale_total_sum'] = $team_sale_total_sum;
        //$team['team_sale_count_sum'] = $team_sale_count_sum;


        return response()->json(['success'=>true, 'team'=>$team, 'team_sale_list'=>$team_sale_list]);

    }

}
