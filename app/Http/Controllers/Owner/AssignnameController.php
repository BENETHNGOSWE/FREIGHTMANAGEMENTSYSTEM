<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Sendername;

class AssignnameController extends Controller
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
        $this->data['sendernames'] = Sendername::pluck('name', 'id');
        return view('dashboards.owner.assignnames.create', $this->data);
    }

    public function validated_sendername(Request $request, Sendername $sendername = null): array
    {
        return $request->validate([
            'client_id' => ['required', 'string', 'exists:clients,id'],
            'sendername_id' => ['required', 'string', 'exists:sendernames,id'],
        ]);
    }

    public function store(Request $request)
    {
        // Validate incoming data
        $request->validate([
            'client_id' => 'required|exists:clients,id', // Ensure the client exists
            'sendername_id' => 'required|exists:sendernames,id', // Ensure the sender name exists
        ]);

        // Find the client
        $client = Client::findOrFail($request->client_id);

        // Attach the sender name to the client
        $client->sendernames()->syncWithoutDetaching([$request->sendername_id]);
        $client->load('sendernames');

        return response()->json([
            'message' => 'Sender name assigned successfully!',
            'client' => $client,
        ]);
    }

    public function show(Request $request, Sendername $sendername): View
    {
        $this->authorize('view', $sendername);
        $this->data['sendername'] = $sendername;
        return view('dashboards.owner.sendernames.show', $this->data);
    }

    public function edit(Sendername $sendername)
    {
        $this->authorize('update', $sendername);
        $this->data['sendername'] = $sendername;
        return view('dashboards.owner.sendernames.edit', $this->data);
    }

    public function update(Request $request, Sendername $sendername)
    {
        $this->authorize('update', $sendername);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);
        $sendername->update($validated);
        return redirect()->route('owner.list.sendername')->with('success', 'Sendername Updated Successfuly');
    }

    public function delete(Request $request, SenderName $sendername)
    {
        $this->authorize('delete', $sendername);
        $sendername->delete();
        return redirect()->route('owner.list.sendername')->with('success', 'Sendername Successfuly deleted');
    }
}
