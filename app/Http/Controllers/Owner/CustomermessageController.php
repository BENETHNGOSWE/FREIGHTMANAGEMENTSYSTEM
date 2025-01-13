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
        $user = auth()->user();

        if (auth()->user()->hasRole('owner')) {
            $this->data['clients'] = Client::with('sendernames')->get();
        } else {
            $this->data['clients'] = Client::with('sendernames')
                ->where('id', $user->client_id)
                ->get();
        }
        return view('dashboards.owner.assignnames.index', $this->data);
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
}
