<?php

namespace App\Http\Controllers\Api;

use App\Actions\Meetings\CreateMeetingAction;
use App\Actions\Meetings\JoinMeetingAction;
use App\Http\Requests\Meetings\StoreMeetingRequest;
use App\Http\Resources\MeetingResource;
use App\Http\Controllers\Controller;
use App\Models\Meeting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class MeetingController extends Controller
{
    public function index(): JsonResponse{
        $meetings = Meeting::with(['users'])
            ->withCount('users')
            ->orderBy('day')
            ->orderBy('start_time')
            ->get();

        return response()->json(['data' => MeetingResource::collection($meetings)], 200);
    }

    public function available(): JsonResponse{
        $today = Carbon::now()->toDateString();

        $meetings = Meeting::with(['users'])
            ->withCount('users')
            ->where('status', 'ACTIVE')
            ->where('day', '>=', $today)
            ->orderBy('day')
            ->orderBy('start_time')
            ->get();

        return response()->json(['data' => MeetingResource::collection($meetings)], 200);
    }

    public function myMeetings(): JsonResponse{
        $meetings = Meeting::with(['users'])
            ->withCount('users')
            ->whereHas('users', function ($q) {
                $q->where('users.id', Auth::id());
            })
            ->orderBy('day')
            ->orderBy('start_time')
            ->get();

        return response()->json(['data' => MeetingResource::collection($meetings)], 200);
    }

    public function store(StoreMeetingRequest $request, CreateMeetingAction $action): JsonResponse
    {    
        $validated = $request->validated();
        $result = $action->execute($validated);

        if (!$result['ok']) {
            return response()->json(['message' => $result['message']], $result['status']);
        }

        return response()->json([
            'message' => 'Meeting created successfully',
            'data' => new MeetingResource($result['meeting']),
        ], 201);
    }

    public function join(int $id, JoinMeetingAction $action): JsonResponse{
        $result = $action->execute($id);

        if (!$result['ok']) {
            return response()->json(['message' => $result['message']], $result['status']);
        }

        return response()->json(['message' => $result['message']], 200);
    }

    public function quit(int $id): JsonResponse{
        $meeting = Meeting::findOrFail($id);

        $inMeeting = $meeting->users()
            ->where('users.id', Auth::id())
            ->exists();

        if (!$inMeeting) {
            return response()->json(['message' => 'You are not in this meeting.'], 422);
        }

        $meeting->users()->detach(Auth::id());

        return response()->json(['message' => 'You have left the meeting.'], 200);
    }

    public function destroy(int $id): JsonResponse{
        $meeting = Meeting::findOrFail($id);
        $meeting->delete();

        return response()->json(['message' => 'Meeting deleted.'], 200);
    }

    public function updateStatus(Request $request, int $id): JsonResponse{
        $validated = $request->validate([
            'status' => ['required', 'in:ACTIVE,FINISHED,CANCELLED'],
        ]);

        $meeting = Meeting::findOrFail($id);
        $meeting->status = $validated['status'];
        $meeting->save();

        return response()->json([
            'message' => 'Status updated',
            'data' => new MeetingResource($meeting->load(['users'])->loadCount('users')),
        ], 200);
    }
}