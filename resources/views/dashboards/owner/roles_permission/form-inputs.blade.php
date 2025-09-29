@php 
    $editing = isset($roles);

    // Group permissions by module
    $groupedPermissions = $permissions->groupBy(function($perm) {
        return explode(' ', $perm->name)[1] ?? 'other';
    });

    // Define actions (columns)
    $actions = ['list', 'view', 'create', 'update', 'delete'];
@endphp

<div class="row">
    {{-- roles Name --}}
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

    {{-- Permissions --}}
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
                                $permission = $perms->firstWhere('name', $permissionName);
                            @endphp
                            <td class="text-center">
                                @if($permission)
                                    <div class="form-check">
                                        <input type="checkbox" 
                                               class="form-check-input"
                                               id="perm-{{ $permission->id }}"
                                               name="permissions[{{ $permission->name }}]"
                                               value="{{ $permission->name }}">
                                        <label class="form-check-label" for="perm-{{ $permission->id }}">
                                            {{-- Optional: show checkmark text --}}
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
