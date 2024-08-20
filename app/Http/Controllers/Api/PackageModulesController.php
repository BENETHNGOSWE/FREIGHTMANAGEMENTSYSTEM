<?php
namespace App\Http\Controllers\Api;

use App\Models\Module;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\ModuleCollection;

class PackageModulesController extends Controller
{
    public function index(Request $request, Package $package): ModuleCollection
    {
        $this->authorize('view', $package);

        $search = $request->get('search', '');

        $modules = $package
            ->modules()
            ->search($search)
            ->latest()
            ->paginate();

        return new ModuleCollection($modules);
    }

    public function store(
        Request $request,
        Package $package,
        Module $module
    ): Response {
        $this->authorize('update', $package);

        $package->modules()->syncWithoutDetaching([$module->id]);

        return response()->noContent();
    }

    public function destroy(
        Request $request,
        Package $package,
        Module $module
    ): Response {
        $this->authorize('update', $package);

        $package->modules()->detach($module);

        return response()->noContent();
    }
}
