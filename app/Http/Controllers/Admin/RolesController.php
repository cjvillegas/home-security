<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyRoleRequest;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class RolesController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('role_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user = auth()->user();

        $user->permissions = $user->getPermissionNameByModule('stock_items');

        return view('admin.roles.index', compact('user'));
    }

    /**
     * Fetch Roles.
     *
     * @return JsonResponse
     */
    public function fetchRoles(Request $request)
    {
        $searchString = $request->searchString;
        $size = $request->size;

        $roles = Role::orderBy('title', 'asc')->with('permissions')
            ->when($searchString, function ($query) use ($searchString) {
                $query->where('title', 'like', "%{$searchString}%");
            });
        $roles = $roles->paginate($size);

        return response()->json(['roles' => $roles]);
    }

    /**
     * Fetch All permissions list
     *
     * @return JsonResponse
     */
    public function fetchAllPermissions()
    {
        $permissions = Permission::all();

        return response()->json(['permissions' => $permissions]);
    }

    /**
     * Store/Save new Role
     *
     * @param  StoreRoleRequest $request
     *
     * @return JsonResponse
     */
    public function store(StoreRoleRequest $request)
    {
        $role = Role::create($request->all());
        $role->permissions()->sync($request->input('permissions', []));

        return response()->json(['message' => 'Role Successfully Saved.']);
    }

    /**
     * Update role's information
     *
     * @param  UpdateRoleRequest $request
     * @param  Role $role
     *
     * @return JsonResponse
     */
    public function update(UpdateRoleRequest $request, Role $role)
    {
        $role->update($request->all());
        $role->permissions()->sync($request->input('permissions', []));

        return response()->json(['message' => 'Role Successfully Updated.']);
    }

    /**
     * Destroy/Delete Role
     *
     * @param  Role $role
     *
     * @return JsonResponse
     */
    public function destroy(Role $role)
    {
        abort_if(Gate::denies('role_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $role->delete();

        return response()->json(['message' => 'Role Successfully Deleted.']);
    }

    public function massDestroy(MassDestroyRoleRequest $request)
    {
        Role::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
