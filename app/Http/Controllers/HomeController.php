<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function import()
    {
        return view('pages.import');
    }

    public function people()
    {
        return view('pages.people.index');
    }

    public function people_show($id)
    {
        return view('pages.people.show', ['id'=>$id]);
    }
}
