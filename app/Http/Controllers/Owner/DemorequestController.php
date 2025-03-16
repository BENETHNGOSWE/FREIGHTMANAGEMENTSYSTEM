<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Demorequest;

class DemorequestController extends Controller
{
    public $data = [];
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index()
    {
       $this->data['demorequests'] = Demorequest::all();
        return view('dashboards.owner.demorequests.index', $this->data);
    }
}
