<?php
namespace App\Http\Controllers\Api;

use App\Models\Module;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\PackageCollection;

class ModulePackagesController extends Controller
{
    public function index(Request $request, Module $module): PackageCollection
    {
        $this->authorize('view', $module);

        $search = $request->get('search', '');

        $packages = $module
            ->packages()
            ->search($search)
            ->latest()
            ->paginate();

        return new PackageCollection($packages);
    }

    public function store(
        Request $request,
        Module $module,
        Package $package
    ): Response {
        $this->authorize('update', $module);

        $module->packages()->syncWithoutDetaching([$package->id]);

        return response()->noContent();
    }

    public function destroy(
        Request $request,
        Module $module,
        Package $package
    ): Response {
        $this->authorize('update', $module);

        $module->packages()->detach($package);

        return response()->noContent();
    }
}
