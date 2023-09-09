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
use App\Models\Setting;

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
        
        $date_range_month = sprintf('%02d', $request->date_range_month);
        $day_count = 0;

        $show_never_open_stall = $request->show_never_open_stall=="true"?true:false;

        $team_stalls = TeamStall::where([['team_id','=', $team_id]])->get();
        $team_sale_list = [];
        $team_sale_total_sum = 0;
        $team_sale_count_sum = 0;

        

        if($date_range_month == -1){
            // Show all
            $from = date('20230610');
            $to = new DateTime(date('Ymd'));
        }else{
            $from = date('Y'.$date_range_month.'01');
            //$to = date('Y'.$date_range_month.'30');
            $last_day = new DateTime(date('Y').'-'.$date_range_month.'-01');
            $last_day->modify('last day of this month');
    
            // Compare last day with today
            $today = new DateTime(date('Ymd'));

            if($today > $last_day){
                $to = $last_day;
            }else{
                $to = $today;
            }
            $to = $to;
        }

        $last_update = new DateTime(date(Setting::where('name', 'import_sales_updated_at')->first()->val));
        $last_update->modify('next day');

        if($last_update < $to){
            //dd($to->format('Ym'), $last_update->format('Ym'));
            if(date('Ym') === $last_update->format('Ym')){
                $to = $last_update;
            }
        }
        //dd($to, $last_update);
        $to = $to->format('Ymd');

        $dates = new DatePeriod(new DateTime($from), new DateInterval('P1D'), new DateTime($to));

        $team_sale_list = array();

        $stall_list = [];
        $stall_no_sale_day_count = array();

        foreach($team_stalls as $cnt=>$stall){
            array_push($stall_list, $stall->stall_number);
            $stall_no_sale_day_count[$cnt] = 0;
        }
        array_push($stall_list, 'รวม');
        $team_sale_list['header'] = $stall_list;

        $stall_sales_by_date = array();
        $stall_sales_sum = array();
        $stall_sales_max = array();
        $stall_sales_min = array();

        $sales_by_date_sum_max = 0;
        $sales_by_date_sum_min = 999999999;

        $team_sum = 0;

        $insight = array();
        $stall_never_open_count = 0;
        $stall_sales_average = array();

        foreach($dates as $i=>$date){
            $day_count++;
            $temp = [];
            $sales_by_date_sum = 0.0;

            foreach($team_stalls as $j=>$stall){
                $sale = Sale::where([
                    ['stall_number', '=', $stall->stall_number],
                    ['sale_date','=', $date]
                    ])->first();
                array_push($temp, number_format($sale->sale_total??0.0, 2));

                if(!array_key_exists($j, $stall_sales_sum)){
                    //$stall_sum[$stall->stall_number] = 0;
                    $stall_sales_sum[$j] = 0;
                }
                if(!array_key_exists($j, $stall_sales_max)){
                    //$stall_sum[$stall->stall_number] = 0;
                    $stall_sales_max[$j] = 0;
                }
                if(!array_key_exists($j, $stall_sales_min)){
                    //$stall_sum[$stall->stall_number] = 0;
                    $stall_sales_min[$j] = number_format($sale->sale_total??0.0, 2);
                }

                //$stall_sum[$stall->stall_number] += $sale->sale_total??0.0;
                $sale_total = number_format($sale->sale_total??0.0, 2);
                $sale_total = str_replace(",", "", $sale_total);

                $stall_sales_sum[$j] += $sale_total;
                $sales_by_date_sum += $sale->sale_total??0.0;

                // Insight
                if($sale_total == 0){
                    $stall_no_sale_day_count[$j]++;
                }

                if($stall_sales_max[$j] < $sale_total){
                    $stall_sales_max[$j] = number_format($sale->sale_total??0.0, 2);
                }

                if($stall_sales_min[$j] > $sale_total){
                    $stall_sales_min[$j] = number_format($sale->sale_total??0.0, 2);
                }

            }
            array_push($temp, number_format($sales_by_date_sum, 2));
            $stall_sales_by_date[$date->format('d/m')] = $temp;

            if($sales_by_date_sum_max < $sales_by_date_sum){
                $sales_by_date_sum_max = number_format($sales_by_date_sum, 2);
            }

            if($sales_by_date_sum_min > $sales_by_date_sum){
                $sales_by_date_sum_min = number_format($sales_by_date_sum, 2);
            }

            
        }
        //dd($sales_by_date);


        // Insight

        // Sum
        $stall_sales_sum[count($stall_list)-1] = number_format(array_sum($stall_sales_sum),2);
        
        $stall_sales_max[count($stall_list)-1] = $sales_by_date_sum_max;
        $stall_sales_min[count($stall_list)-1] = $sales_by_date_sum_min;

        foreach($stall_sales_sum as $index=>$sum){
            $sum = str_replace(",", "", $sum);
            $stall_sales_sum[$index] = number_format(floatval($sum), 2);

            $stall_sales_average[$index] = number_format(floatval($sum) / $day_count, 2);

            if(floatval($sum) == 0){
                $stall_never_open_count++;

                // Remove Never Open Stall
                if(!$show_never_open_stall){
                    foreach($stall_sales_by_date as $l=>$date){
                        unset($stall_sales_by_date[$l][$index]);
                    }
                    unset($stall_sales_sum[$index]);
                    unset($stall_sales_average[$index]);
                    unset($stall_sales_max[$index]);
                    unset($stall_sales_min[$index]);

                    unset($stall_no_sale_day_count[$index]);

                    unset($team_sale_list["header"][$index]);
                }

                
            }
        }


//        $stall_sales_by_date['รวม'] = $stall_sales_sum;
        $team_sale_list['sales_by_date'] = $stall_sales_by_date;

        $insight['stall_never_open_count'] = $stall_never_open_count;
        $insight['stall_no_sale_day_count'] = $stall_no_sale_day_count;
        $insight['stall_sales_sum'] = $stall_sales_sum;
        $insight['stall_sales_average'] = $stall_sales_average;
        $insight['stall_sales_max'] = $stall_sales_max;
        $insight['stall_sales_min'] = $stall_sales_min;
        
        $insight['day_count'] = $day_count;
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


        return response()->json(['success'=>true, 'team'=>$team, 'team_sale_list'=>$team_sale_list, 'insight'=>$insight]);

    }

}
