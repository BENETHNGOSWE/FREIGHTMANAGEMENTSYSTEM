@extends('layouts._master')

@php
    // Determine if we are editing (assuming $role is passed by the controller, 
    // but using $roles as per your existing code for consistency)
    $editing = isset($roles);

    // 1. Get a collection of the role's current permission names for fast lookup
    $currentPermissions = $editing ? $roles->permissions->pluck('name') : collect();

    // Group all available permissions by module (e.g., 'list users' -> 'users')
    $groupedPermissions = $permissions->groupBy(function($perm) {
        // Use the second word after splitting by space as the module name
        return explode(' ', $perm->name)[1] ?? 'other';
    });

    // Define standard CRUD actions (table columns)
    $actions = ['list', 'view', 'create', 'update', 'delete'];
@endphp

@section('content')
    <div class="container-fluid">
        <div class="form-head align-items-center d-flex mb-sm-4 mb-3">
            <div class="me-auto">
                <h2 class="text-black font-w600">Edit Role: {{ $roles->name }}</h2>
            </div>
            <div>
                <p class="mb-1">Dashboard / <span><a class="text-primary" href="{{ route('roles.index') }}">Roles List</a></span></p>
            </div>
        </div>
        <div class="card">
            <div class="card-body">

                {{-- Ensure $roles is the Role object for the route --}}
                <x-form method="PUT" action="{{ route('roles.update', $roles) }}" class="mt-4">
                    <div class="row">
                        {{-- Role Name --}}
                        <x-inputs.group class="col-sm-6 mb-4">
                            <x-inputs.text
                                name="name"
                                label="Role Name"
                                :value="old('name', ($editing ? $roles->name : ''))"
                                maxlength="255"
                                placeholder="Role Name"
                                required
                            />
                        </x-inputs.group>

                        {{-- Permissions Table --}}
                        <div class="col-sm-12">
                            <h3 class="mb-3">Permissions</h3>

                            <table class="table table-bordered table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th>Module</th>
                                        @foreach ($actions as $action)
                                            <th class="text-center text-capitalize">{{ $action }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($groupedPermissions as $module => $perms)
                                        <tr>
                                            <td class="fw-bold text-capitalize">{{ $module }}</td>
                                            @foreach ($actions as $action)
                                                @php
                                                    $permissionName = $action . ' ' . $module;
                                                    // Find the full permission object
                                                    $permission = $perms->firstWhere('name', $permissionName);
                                                    
                                                    // 2. Check if the current role has this permission
                                                    $hasPermission = $currentPermissions->contains($permissionName);
                                                @endphp
                                                <td class="text-center">
                                                    @if($permission)
                                                        <div class="form-check">
                                                            <input type="checkbox"
                                                                class="form-check-input"
                                                                id="perm-{{ $permission->id }}"
                                                                name="permissions[]"
                                                                value="{{ $permission->name }}"
                                                                {{ $hasPermission ? 'checked' : '' }}
                                                            >
                                                            <label class="form-check-label" for="perm-{{ $permission->id }}">
                                                                {{-- Leave label empty for centered checkmark --}}
                                                            </label>
                                                        </div>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('roles.index') }}" class="btn btn-light">
                            <i class="fa-solid fa-backward"></i>
                            @lang('crud.common.back')
                        </a>

                        <a href="{{ route('roles.create') }}" class="btn btn-light">
                            <i class="icon ion-md-add text-primary"></i>
                            @lang('crud.common.create')
                        </a>

                        <button type="submit" class="btn btn-primary float-right">
                            <i class="icon ion-md-save"></i>
                            @lang('crud.common.update')
                        </button>
                    </div>
                </x-form>
            </div>
        </div>
    </div>
@endsection
