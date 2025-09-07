<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Container;
use App\Models\ClientAdmin;
use App\Models\Containerloading;
use App\Models\Loadpackage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;
use Barryvdh\DomPDF\Facade\Pdf;

class ContainerController extends Controller
{
    protected $data = [];

    protected $fms_id = null;

    public function __construct()
    {
        $this->middleware('multiple');
        $this->middleware(function ($request, $next) {
            if (Auth::check()) {
                $this->fms_id = Auth::user()->client_id;
                $this->data['clientadmin'] = ClientAdmin::where('client_id', $this->fms_id)->first();
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
            'container_status' => 'required|in:Warehouse,OnVessel,Arrived_TZ-Port,ICD,Warehouse-HQ',
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
            'container_status' => 'required|in:Warehouse,OnVessel,Arrived_TZ-Port,ICD,Warehouse-HQ',
        ]);
        $validated['client_id'] = $this->fms_id;
        $container->update($validated);
        return redirect()->route('admin.list.container')->with('success', 'Container Updated Successfuly');
    }

    public function delete(Request $request, $container)
    {
        $container = Container::where('id', $container)->where('client_id', $this->fms_id)->first();
        $this->authorize('delete', $container);
        $container->delete();
        return redirect()->route('admin.list.container')->with('success', 'Container Deleted Successfuly ');
    }

    // public function loadContainer($containerId)
    // {
    //     $container = Container::findOrFail($containerId);
    //     $availableLoads = Loadpackage::whereNull('container_id')->orderBy('load_volume', 'desc')->get();

    //     $selectedLoads = $this->findOptimalLoadCombination($availableLoads, $container->container_volume);

    //     if ($selectedLoads) {
    //         foreach ($selectedLoads as $load) {
    //             $load->container_id = $containerId;
    //             $load->save();

    //             Containerloading::create([
    //                 'loadpackage_id' => $load->id,
    //                 'container_id' => $containerId,
    //                 'client_id' => $load->client_id,
    //             ]);
    //         }

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Loads successfully added to container',
    //             'loads' => $selectedLoads,
    //         ]);
    //     }

    //     return response()->json([
    //         'success' => false,
    //         'message' => 'No optimal load combination found',
    //     ]);
    // }

    // // Recursive function to find optimal load combination
    // private function findOptimalLoadCombination($loads, $containerVolume, $currentCombination = [], $currentVolume = 0)
    // {
    //     if (abs($currentVolume - $containerVolume) < 0.1) {
    //         return $currentCombination;
    //     }

    //     if ($currentVolume > $containerVolume) {
    //         return null;
    //     }

    //     foreach ($loads as $index => $load) {
    //         if (in_array($load, $currentCombination)) {
    //             continue;
    //         }

    //         $newCombination = $currentCombination;
    //         $newCombination[] = $load;
    //         $newVolume = $currentVolume + $load->load_volume;

    //         $result = $this->findOptimalLoadCombination($loads, $containerVolume, $newCombination, $newVolume);

    //         if ($result !== null) {
    //             return $result;
    //         }
    //     }

    //     return null;
    // }

    public function loadContainer($containerId)
    {
        $container = Container::findOrFail($containerId);
        $availableLoads = Loadpackage::whereNull('container_id')->orderBy('load_volume', 'desc')->get();

        // Define tolerance levels as absolute volume differences
        $toleranceLevels = [
            0.1, 
            0.5, 
            1.0, 
            2.0, 
        ];

        $selectedLoads = null;
        $bestDifference = PHP_FLOAT_MAX;
        $bestTolerance = null;

        foreach ($toleranceLevels as $tolerance) {
            $result = $this->findOptimalLoadCombination($availableLoads, $container->container_volume, tolerance: $tolerance);

            if ($result) {
                $totalVolume = collect($result)->sum('load_volume');
                $difference = abs($container->container_volume - $totalVolume);

                
                if ($difference < $bestDifference) {
                    $selectedLoads = $result;
                    $bestDifference = $difference;
                    $bestTolerance = $tolerance;
                }
            }
        }

        if ($selectedLoads) {
            $totalVolume = collect($selectedLoads)->sum('load_volume');
            $volumeDifference = abs($container->container_volume - $totalVolume);
            $volumeDifferencePercentage = ($volumeDifference / $container->container_volume) * 100;

            foreach ($selectedLoads as $load) {
                $load->container_id = $containerId;
                $load->save();
                Containerloading::create([
                    'loadpackage_id' => $load->id,
                    'container_id' => $containerId,
                    'client_id' => $load->client_id,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => sprintf('Loads successfully added to container (Volume difference: %.3f units / %.2f%%)', $volumeDifference, $volumeDifferencePercentage),
                'loads' => $selectedLoads,
                'total_volume' => round($totalVolume, 3),
                'container_volume' => round($container->container_volume, 3),
                'volume_difference' => round($volumeDifference, 3),
                'volume_difference_percentage' => round($volumeDifferencePercentage, 3),
                'used_tolerance' => $bestTolerance,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No suitable load combination found',
        ]);
    }

    private function findOptimalLoadCombination($loads, $containerVolume, $currentCombination = [], $currentVolume = 0, $tolerance = 0.5, $bestCombination = null, $bestDifference = PHP_FLOAT_MAX)
    {
        // Calculate absolute difference
        $currentDifference = abs($containerVolume - $currentVolume);

        // If current combination is better than the best found so far and within tolerance
        if ($currentVolume > 0 && $currentDifference <= $tolerance && $currentDifference < $bestDifference) {
            $bestCombination = $currentCombination;
            $bestDifference = $currentDifference;
        }

        // Stop if we've exceeded the container volume by more than the tolerance
        if ($currentVolume > $containerVolume + $tolerance) {
            return $bestCombination;
        }

        // Stop if remaining volume is too small to be meaningful
        if ($containerVolume - $currentVolume < 0.001) {
            return $bestCombination;
        }

        foreach ($loads as $index => $load) {
            if (in_array($load, $currentCombination)) {
                continue;
            }

            $newCombination = $currentCombination;
            $newCombination[] = $load;
            $newVolume = $currentVolume + $load->load_volume;

            $result = $this->findOptimalLoadCombination($loads, $containerVolume, $newCombination, $newVolume, $tolerance, $bestCombination, $bestDifference);

            if ($result !== null) {
                $resultVolume = collect($result)->sum('load_volume');
                $resultDifference = abs($containerVolume - $resultVolume);

                if ($resultDifference < $bestDifference) {
                    $bestCombination = $result;
                    $bestDifference = $resultDifference;
                }
            }
        }

        return $bestCombination;
    }

    public function generateContainerloads($containerId)
    {
        $container = Container::findOrFail($containerId);
        $loads = Containerloading::where('container_id', $containerId)
            ->with(['loadpackage', 'client'])
            ->get();

        if ($loads->isEmpty()) {
            return back()->with('error', 'No loads found for this container.');
        }

        $client = $container->containerloadings->first()->client;

        if (!$client) {
            return back()->with('error', 'Client information is missing for this container.');
        }

        $totalLoadVolume = $container->containerloadings->sum(function ($loading) {
            return $loading->loadpackage->load_volume ?? 0;
        });

        $pdf = Pdf::loadView('dashboards.admin.containers.containerloadspdf', [
            'container' => $container,
            'loads' => $loads,
            'client' => $client,
            'totalLoadVolume' => $totalLoadVolume,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('container-loadlist-' . $container->id . '.pdf');
    }
}
