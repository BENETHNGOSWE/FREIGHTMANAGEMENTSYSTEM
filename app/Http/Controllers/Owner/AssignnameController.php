<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AssignnameController extends Controller
{
    public $data = [];
    public function __construct()
    {
        $this->middleware(['auth', 'owner']);
    }

    public function index()
    {
        $this->data['sendernames'] = Sendername::all();
        return view('dashboards.owner.sendernames.index', $this->data);
    }

    public function create()
    {
        $this->authorize('create', Sendername::class);
        return view('dashboards.owner.sendernames.create', $this->data);
    }

    public function validated_sendername(Request $request, Sendername $sendername = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Sendername::class);
        $validated = $this->validated_sendername($request);
        try {
            DB::beginTransaction();
            $sendername = new Sendername();
            $sendername->fill($validated);
            $sendername->save();
            DB::commit();

        } catch (\Throwable $th) {
            \DB::rollback();
            \Log::error($th->getMessage() . '' . $th->getTraceAsString());
            return redirect()->back()->with('error', 'Something went wrong');
        }
        return redirect()->route('owner.list.sendername')->with('success', 'Sendername Added Successfuly');
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

    public function delete(Request $request,SenderName $sendername)
    {
       
        $this->authorize('delete', $sendername);
        $sendername->delete();
        return redirect()->route('owner.list.sendername')->with('success', 'Sendername Successfuly deleted');
    }
}
