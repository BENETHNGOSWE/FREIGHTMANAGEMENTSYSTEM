<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OwnerController extends Controller
{
    protected $data = [];
    public function __construct()
    {
        $this->middleware(['auth', 'owner']);
    }

    public function index()
    {
        return view('dashboards.owner.dashboard', $this->data);
    }
}
