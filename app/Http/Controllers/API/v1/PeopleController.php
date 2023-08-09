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
use App\Models\People;
use DateTime;
use DatePeriod;
use DateInterval;
use stdClass;

class PeopleController extends Controller
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

    public function index(Request $request)
    {
        $people = People::orderBy('id')->get();

        $people_temp = array();
        foreach($people as $i=>$person){
            $temp = $person;
            array_push($people_temp, $temp);
        }

        return response()->json(['success'=>true, 'people'=>$people_temp]);
    }


}
