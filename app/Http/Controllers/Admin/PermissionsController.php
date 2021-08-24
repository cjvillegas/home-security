<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyPermissionRequest;
use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class PermissionsController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('permission_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user = auth()->user();

        $user->permissions = $user->getPermissionNameByModule('stock_items');

        return view('admin.permissions.index', compact('user'));
    }

    /**
     * Fetch Permissions List
     *
     * @param  mixed $request
     *
     * @return JsonResponse
     */
    public function fetchPermissions(Request $request)
    {
        $searchString = $request->searchString;
        $size = $request->size;

        $permissions = Permission::orderBy('title', 'asc')
            ->when($searchString, function ($query) use ($searchString) {
                $query->where('title', 'like', "%{$searchString}%");
            });
        $permissions = $permissions->paginate($size);

        return response()->json(['permissions' => $permissions]);
    }

    /**
     * Save new Permission
     * @return JsonResponse
     */
    public function store(StorePermissionRequest $request)
    {
        $permission = Permission::create($request->all());

        return response()->json(['message' => 'Permission Successfully Saved.']);
    }

    /**
     * Update Permission's data
     *
     * @param  mixed $request
     * @param  mixed $permission
     * @return JsonResponse
     */
    public function update(UpdatePermissionRequest $request, Permission $permission)
    {
        $permission->update($request->all());

        return response()->json(['message' => 'Permission Successfully Updated.']);
    }

    /**
     * Delete Permission
     *
     * @param  mixed $permission
     * @return JsonResponse
     */
    public function destroy(Permission $permission)
    {
        abort_if(Gate::denies('permission_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $permission->delete();

        return response()->json(['message' => 'Permission Successfully Deleted.']);
    }

    public function massDestroy(MassDestroyPermissionRequest $request)
    {
        Permission::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
