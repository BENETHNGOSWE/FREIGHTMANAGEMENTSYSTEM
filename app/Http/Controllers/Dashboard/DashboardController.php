<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public $data = [];
    public function __construct()
    {
        $this->middleware('permission:view marks', ['only' => ['index']]);
        $this->middleware('permission:create marks', ['only' => ['create', 'store']]);
        $this->middleware('permission:update marks', ['only' => ['update', 'edit']]);
        $this->middleware('permission:delete marks', ['only' => ['destroy']]);
        // $this->data['marks'] = Mark::all();
    }


    public function index(){
        // if(auth()->user()->hasRole('super-admin')){
        //     $this->data['subjects'] = Subject::all();
        //     $this->data['jumlayamasomo'] = Subject::all()->count();

        // }else{
        //     $this->data['subjects'] = auth()->user()->subjects;
        //     $this->data['jumlayamasomo'] = auth()->user()->subjects->count();
        // }
        
        return view('dashboard/owner.dashboard', $this->data);
    }
}
