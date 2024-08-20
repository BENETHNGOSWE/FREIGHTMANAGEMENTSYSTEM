<?php

namespace App\Http\Controllers\Api;

use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\ModuleResource;
use App\Http\Resources\ModuleCollection;
use App\Http\Requests\ModuleStoreRequest;
use App\Http\Requests\ModuleUpdateRequest;

class ModuleController extends Controller
{
    public function index(Request $request): ModuleCollection
    {
        $this->authorize('view-any', Module::class);

        $search = $request->get('search', '');

        $modules = Module::search($search)
            ->latest()
            ->paginate();

        return new ModuleCollection($modules);
    }

    public function store(ModuleStoreRequest $request): ModuleResource
    {
        $this->authorize('create', Module::class);

        $validated = $request->validated();

        $module = Module::create($validated);

        return new ModuleResource($module);
    }

    public function show(Request $request, Module $module): ModuleResource
    {
        $this->authorize('view', $module);

        return new ModuleResource($module);
    }

    public function update(
        ModuleUpdateRequest $request,
        Module $module
    ): ModuleResource {
        $this->authorize('update', $module);

        $validated = $request->validated();

        $module->update($validated);

        return new ModuleResource($module);
    }

    public function destroy(Request $request, Module $module): Response
    {
        $this->authorize('delete', $module);

        $module->delete();

        return response()->noContent();
    }
}
