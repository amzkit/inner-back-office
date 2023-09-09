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

        return response()->json(['success'=>true, 'people'=>$people_temp, 'people_role'=>config('people.role')]);
    }

    public function show(Request $request)
    {
        $id = $request->id??null;
        if($id == "create"){
            return response()->json(['success'=>false]);
        }

        $people = People::find($id);

        return response()->json(['success'=>true, 'people'=>$people, 'people_role'=>config('people.role'), 'people_gender'=>config('people.gender')]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'person.firstname' => 'required|max:255',
            'person.lastname' => 'required|max:255',
            'person.firstname_th' => 'max:255',
            'person.lastname_th' => 'max:255',
        ]);

        $person = $request->person;

        if(isset($person['id'])){
            $temp = People::find($person['id']);
            $temp->firstname = $person['firstname'];
            $temp->lastname = $person['lastname'];
            $temp->firstname_th = $person['firstname_th'];
            $temp->lastname_th = $person['lastname_th'];
            $temp->gender = $person['gender'];
            $temp->mobile_number = $person['mobile_number'];
            $temp->date_of_birth = $person['date_of_birth'];
            $temp->national_id_number = $person['national_id_number'];
            $temp->save();
        }else{
            $person = new People($request->person);
            $person->save();
        }

        return response()->json(['success'=>true, 'person'=>$person]);

    }
}
