<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Container;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;

class ContainerController extends Controller
{
    protected $data = [];

    protected $fms_id = null;

    public function __construct()
    {
        $this->middleware('admin');
        $this->middleware(function ($request, $next) {
            if (Auth::check()) {
                $this->fms_id = Auth::user()->client_id;
            }

            return $next($request);
        });
    }

    public function index()
    {
        $this->data['containers'] = Container::where('client_id', $this->fms_id)->get();
        return view('dashboards.admin.containers.index', $this->data);
    }

    public function create()
    {
        $this->authorize('create', Container::class);
        return view('dashboards.admin.containers.create', $this->data);
    }

    public function validated_container(Request $request, Container $container = null): array
    {
        return $request->validate([
            'container_volume' => ['required', 'string', 'max:255'],
            'container_size' => ['required', 'string', 'max:255'],
            'container_number' => ['required', 'string', 'max:255'],
            'container_status' => 'required|in:portstation,onmovement,delivery,closed',
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Container::class);
        $validated = $this->validated_container($request);
        try {
            DB::beginTransaction();
            $container = new Container();
            $validated['client_id'] = $this->fms_id;
            $container->fill($validated);
            $container->save();
            DB::commit();
        } catch (\Throwable $th) {
            \DB::rollback();
            \Log::error($th->getMessage() . '' . $th->getTraceAsString());
            return redirect()->back()->with('error', 'Something went wrong');
        }
        return redirect()->route('admin.list.container')->with('success', 'Container Added Successfuly');
    }

    public function show(Request $request, Container $container): View
    {
        $this->authorize('view', $container);
        $this->data['container'] = $container;
        return view('dashboards.admin.containers.show', $this->data);
    }

    public function edit(Container $container)
    {
        $this->authorize('update', $container);
        $this->data['container'] = $container;
        return view('dashboards.admin.containers.edit', $this->data);
    }

    public function update(Request $request, Container $container)
    {
        $this->authorize('update', $container);
        $validated = $request->validate([
            'container_volume' => ['required', 'string', 'max:255'],
            'container_size' => ['required', 'string', 'max:255'],
            'container_number' => ['required', 'string', 'max:255'],
            'container_status' => 'required|in:portstation,onmovement,delivery,closed',
        ]);
        $validated['client_id'] = $this->fms_id;
        $container->update($validated);
        return redirect()->route('admin.list.container')->with('success', 'Container Updated Successfuly');
    }

    public function delete(Request $request, $container)
    {
        $container = Container::where('id', $container)
            ->where('client_id', $this->fms_id)
            ->first();
        $this->authorize('delete', $container);
        $container->delete();
        return redirect()->route('admin.list.container')->with('success', 'Container Deleted Successfuly ');
    }
}
