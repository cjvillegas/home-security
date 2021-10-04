<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyTeamRequest;
use App\Http\Requests\StoreTeamRequest;
use App\Http\Requests\UpdateTeamRequest;
use App\Models\ShiftAssignment;
use App\Models\Team;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class TeamsController extends Controller
{
    use CsvImportTrait;

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        abort_if(Gate::denies('team_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.teams.index');
    }

    /**
     * Fetch Teams List
     *
     * @param  mixed $request
     *
     * @return JsonResponse
     */
    public function fetchTeams(Request $request)
    {
        $searchString = $request->searchString;
        $size = $request->size;

        $teams = Team::orderBy('name', 'asc')
            ->when($searchString, function ($query) use ($searchString) {
                $query->where('name', 'like', "%{$searchString}%")
                    ->orWhere('target', 'like', "%{$searchString}%");
            });
        $teams = $teams->paginate($size);

        return response()->json(['teams' => $teams]);
    }

    /**
     * Store/Save new Team
     *
     * @param  StoreTeamRequest $request
     *
     * @return JsonResponse
     */
    public function store(StoreTeamRequest $request)
    {
        $team = Team::create($request->all());

        return response()->json(['message' => 'Team Successfully Saved.']);
    }

    /**
     * Update Team's Information
     *
     * @param  UpdateTeamRequest $request
     * @param  Team $team
     *
     * @return JsonResponse
     */
    public function update(UpdateTeamRequest $request, Team $team): JsonResponse
    {
        $team->update($request->all());

        return response()->json([
            'data' => $team->refresh(),
            'message' => 'Team Successfully Updated.'
        ]);
    }

    /**
     * Destroy/Delete Team
     *
     * @param  Team $team
     *
     * @return JsonResponse
     */
    public function destroy(Team $team): JsonResponse
    {
        abort_if(Gate::denies('team_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $team->delete();

        return response()->json(['message' => 'Team Successfully Deleted.']);
    }

    /**
     * @param MassDestroyTeamRequest $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function massDestroy(MassDestroyTeamRequest $request)
    {
        Team::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Get all teams
     *
     * @return JsonResponse
     */
    public function getAllTeams(): JsonResponse
    {
        return response()->json(Team::get());
    }

    /**
     * Retrieve all folder names. This data is extracted from the shift assignments table
     *
     * @return JsonResponse
     */
    public function shiftAssignmentFolders(): JsonResponse
    {
        $folders = ShiftAssignment::select('folder_name')
            ->groupBy('folder_name')
            ->get()
            ->pluck('folder_name');

        return response()->json($folders);
    }
}
