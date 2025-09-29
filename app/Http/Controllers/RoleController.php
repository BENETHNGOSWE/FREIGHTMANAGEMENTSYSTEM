<?php

namespace  App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use App\Models\ClientAdmin;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('permission:view role', ['only' => ['index']]);
    //     $this->middleware('permission:create role', ['only' => ['create','store','addPermissionToRole','givePermissionToRole']]);
    //     $this->middleware('permission:update role', ['only' => ['update','edit']]);
    //     $this->middleware('permission:delete role', ['only' => ['destroy']]);
    // }
    protected $fms_id = null;
    protected $data = [];
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
        $clientID = auth::user()->client_id;
        $this->data['roles'] = Role::where('client_id', $clientID)->orWhereNull('client_id')->get();
        return view('dashboards.owner.roles_permission.index', $this->data);
    }

    public function create()
    {
        $this->data["permissions"] = Permission::where('scope', 'client')->get();
        return view('dashboards.owner.roles_permission.createroles', $this->data);
    }

    public function store(Request $request)
    {
       $request->validate([
        'name' => 'required'
       ]);

       $clientID = Auth::user()->client_id;
       $guardName = 'web';

       $role = Role::create([
           "name" => $request->name, 
           "client_id" => $clientID, 
           "guard_name" => $guardName
       ]);
       
       $role->syncPermissions($request->permissions);
       return redirect('roles')->with('status','Role Created Successfully');
    }

    public function edit($id)
    {
        $this->data['roles'] = Role::find($id);
        $this->data['permissions'] = Permission::all();
        return view('dashboards.owner.roles_permission.edit', $this->data);

    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'unique:roles,name,'.$role->id
            ],
            'permissions' => 'array'
        ]);

        $role->update([
            'name' => $request->name
        ]);

        $role->syncPermissions($request->permissions ?? []);

        return redirect('roles')->with('status','Role Updated Successfully');
    }

    public function destroy($roleId)
    {
        $role = Role::find($roleId);
        $role->delete();
        return redirect('roles')->with('status','Role Deleted Successfully');
    }

    public function addPermissionToRole($roleId)
    {
        $permissions = Permission::get();
        $role = Role::findOrFail($roleId);
        $rolePermissions = DB::table('role_has_permissions')
                                ->where('role_has_permissions.role_id', $role->id)
                                ->pluck('role_has_permissions.permission_id','role_has_permissions.permission_id')
                                ->all();

        return view('role-permission.role.add-permissions', [
            'role' => $role,
            'permissions' => $permissions,
            'rolePermissions' => $rolePermissions
        ]);
    }

    public function givePermissionToRole(Request $request, $roleId)
    {
        $request->validate([
            'permission' => 'required'
        ]);

        $role = Role::findOrFail($roleId);
        $role->syncPermissions($request->permission);

        return redirect()->back()->with('status','Permissions added to role');
    }


    public function roleByPermission(Request $request, $roleId) {
        $roles = Role::find($roleId);

        if($roles) {
            $permissions = $roles->permissions;

            foreach($permissions as $permission){
                echo $permission->name . "<br>";
            }
        }else {
            echo "Role not found";
        }
        return view('dashboards.owner.roles_permission.show', ['role' => $roles]);

    }
}