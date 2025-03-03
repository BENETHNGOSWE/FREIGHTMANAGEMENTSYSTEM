<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Customermessage;
use Illuminate\Support\Facades\DB;

class CustomermessageController extends Controller
{
    public $data = [];
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index()
    {
       $this->data['customermessages'] = Customermessage::all();
        return view('dashboards.owner.customermessages.index', $this->data);
    }

    public function create()
    {
        // $this->authorize('create', Sendername::class);
        $this->data['clients'] = Client::pluck('client_name', 'id');
        return view('dashboards.owner.customermessages.create', $this->data);
    }

    public function validated_customermessage(Request $request, Customermessage $customermessage = null): array
    {
        return $request->validate([
            'client_id' => ['required', 'string', 'exists:clients,id'],
            'messageamount' => ['required', 'integer'],
        ]);
    }

    public function store(Request $request)
    {
        // $this->authorize('create', Sendername::class);
        $validated = $this->validated_customermessage($request);
        try {
            DB::beginTransaction();
            $customermessage = new Customermessage();
            $customermessage->fill($validated);
            $customermessage->save();
            DB::commit();
        } catch (\Throwable $th) {
            \DB::rollback();
            \Log::error($th->getMessage() . '' . $th->getTraceAsString());
            return redirect()->back()->with('error', 'Something went wrong');
        }
        return redirect()->route('owner.list.customermessage')->with('success', 'Customermessage Added Successfuly');
    }


    public function edit(Request $request, Customermessage $customermessage){
        $this->data['customermessage'] = $customermessage;
        $this->data['clients'] = Client::pluck('client_name', 'id');
        return view('dashboards.owner.customermessages.edit', $this->data);
    }

    public function update(Request $request, Customermessage $customermessage){
        $validate = $request->validate([
            'client_id' => ['required', 'string', 'exists:clients,id'],
            'messageamount' => ['required', 'integer'],
        ]);
        $customermessage->update($validate);
        return redirect()->route('owner.list.customermessage')->with('success', 'Customermessage Update Successfuly');
    }

    public function delete(Request $request,Customermessage $customermessage)
    {
       
        $this->authorize('delete', $customermessage);
        $customermessage->delete();
        return redirect()->route('owner.list.customermessage')->with('success', 'Customermessage Successfuly deleted');
    }
}
