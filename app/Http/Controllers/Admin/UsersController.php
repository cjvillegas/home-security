<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class UsersController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('user_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $users = User::with(['roles'])->get();
        $roles = Role::get();
        $permissions = auth()->user()->getPermissionNameByModule('user');

        return view('admin.users.index', compact('users', 'roles', 'permissions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreUserRequest  $request
     *
     * @return JsonResponse
     */
    public function store(StoreUserRequest $request)
    {
        $user = User::create($request->all());
        $user->roles()->sync($request->input('roles', []));

        return response()->json($user);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  UpdateUserRequest  $request
     * @param  User $user
     *
     * @return JsonResponse
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $user->update($request->all());
        $user->roles()->sync($request->input('roles', []));

        return response()->json($user->refresh()->loadMissing(['roles']));
    }

    /**
     * Fetch the specified resource.
     *
     * @param  User  $employee
     *
     * @return JsonResponse
     */
    public function show(User $user)
    {
        abort_if(Gate::denies('user_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user->load('roles');

        return response()->json($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  User $user
     *
     * @return JsonResponse
     */
    public function destroy(User $user)
    {
        abort_if(Gate::denies('user_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user->delete();

        if ($user->delete()) {
            // modify the deleted email and barcode so that it will not cause conflict
            // to the newly created process category.
            // This is useful because email and barcode are unique in the DB level and we are only soft deleting
            // data in the users table
            $now = now()->unix();
            $user->email = $user->email . "_deleted_$now";
            $user->barcode = $user->barcode . "_deleted_$now";
            $user->save();

            return response()->json(true);
        }

        return response()->json(true);
    }

    public function massDestroy(MassDestroyUserRequest $request)
    {
        User::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Fetch list of process categories
     *
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function getList(Request $request)
    {
        $searchString = $request->get('searchString');
        $status = $request->get('status');
        $size = $request->get('size');

        $users = User::
        orderBy('created_at', 'desc')
            ->with(['roles' => function ($query) {
                $query->select('id', 'title');
            }])
            ->when($searchString, function ($query) use ($searchString) {
                $query->where('name', 'like', "%{$searchString}%");
                $query->orWhere('email', 'like', "%{$searchString}%");
            })
            ->when($status !== null, function ($query) use ($status) {
                $query->where('users.is_active', $status);
            });

        if ($size) {
            $users = $users->paginate($size);
        } else {
            $users = $users->get();
        }

        return response()->json($users);
    }

    /**
     * Fetch list of users without any additional data added
     *
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function getCleanUsers(Request $request)
    {
        $users = User::orderBy('created_at', 'desc')->get();

        return response()->json($users);
    }

    /**
     * Update the status of a user.
     *
     * @param  User $user
     *
     * @return JsonResponse
     */
    public function changeStatus(User $user)
    {
        $user->is_active = !$user->is_active;
        $user->save();

        return response()->json($user);
    }

    /**
     * Return the current authenticated user of the session
     *
     * @return JsonResponse
     */
    public function getAuthUser()
    {
        $user = auth()->user();

        $user->permissions = $user->getPermissionsPerModules('qc_tag');

        return response()->json($user);
    }

    /**
     * Check if user has privacy mode on
     *
     * @return void
     */
    public function checkPrivacy()
    {
        return response()->json(Gate::allows('privacy_on'));
    }
}
