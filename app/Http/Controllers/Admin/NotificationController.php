<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Mockery\Matcher\Not;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('admin.notifications.index');
    }

    /**
     * Display the specified resource.
     *
     * @param Notification $notification
     * @return JsonResponse
     */
    public function show(Notification $notification)
    {
        return response()->json($notification);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Notification $notification
     *
     * @return JsonResponse
     */
    public function destroy(Notification $notification)
    {
        $deleted = $notification->delete();

        return response()->json($deleted);
    }

    /**
     * Fetch list of notifications
     *
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function getList(Request $request)
    {
        $searchString = $request->get('searchString');
        $size = $request->get('size');

        $query = Notification::
            when($searchString, function ($query) use ($searchString) {
                $query->where('data', 'like', "%{$searchString}%");
            })
            ->orderBy('created_at', 'desc');

        $notifications = $query->paginate($size);

        return response()->json($notifications);
    }
}
