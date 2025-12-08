<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\User;
use Illuminate\Http\Request;
use App\Response\ApiResponse;
use App\Services\TripService;
use App\Http\Requests\AddMemberRequest;
use App\Http\Requests\CreateTripRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Requests\UpdateTripRequest;
use Symfony\Component\HttpFoundation\Response;

class TripController extends Controller
{
    protected TripService $tripService;
    public function __construct(TripService $tripService)
    {
        $this->tripService =  $tripService;
    }

    public function index(Request $request)
    {
       $trips = $this->tripService->listForUser(auth()->id());

        return ApiResponse::setMessage('Trips fetched successfully')
            ->setData($trips)
            ->response(Response::HTTP_OK);
    }

    public function store(CreateTripRequest $request)
    {
        $trip = $this->tripService->createTrip($request->validated(), auth()->user()['id']);

        return ApiResponse::setMessage('Trip created successfully')
            ->setData($trip)
            ->response(Response::HTTP_CREATED);
    }
    public function show(Trip $trip)
    {
        // $trip = $this->tripService->getTrip($tripId, auth()->id());

        return ApiResponse::setMessage('Trip details fetched')
            ->setData($trip)
            ->response(Response::HTTP_OK);
    }

    public function update(UpdateTripRequest $request, Trip $trip)
    {
        $trip = $this->tripService->updateTrip($trip, $request->validated(), auth()->id());

        return ApiResponse::setMessage('Trip updated successfully')
            ->setData($trip)
            ->response(Response::HTTP_OK);
    }

    public function destroy(Trip $trip)
    {
        $this->tripService->deleteTrip($trip, auth()->id());

        return ApiResponse::setMessage('Trip deleted successfully')
            ->response(Response::HTTP_OK);
    }

    public function addMember(Request $request, Trip $trip)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role'    => 'nullable|in:admin,traveler',
        ]);

        $member = $this->tripService->addMember(
            $trip,
            $validated['user_id'],
            $validated['role'] ?? 'traveler',
            auth()->id()
        );

        return ApiResponse::setMessage('Member added successfully')
            ->setData($member)
            ->response(Response::HTTP_CREATED);
    }

        public function removeMember(Trip $trip, $userId)
    {
        $this->tripService->removeMember($trip, $userId, auth()->id());
        return ApiResponse::setMessage('Member removed')->response();
    }

    // public function updateRole(UpdateRoleRequest $request, Trip $trip)
    // {
    //     $user = $request->user();
    //     $member = User::findOrFail($request->user_id);

    //     $trip = $this->tripService->updateUserRole($trip, $user, $member, $request->role);

    //     return ApiResponse::setMessage('Member role updated successfully')
    //         ->setData($trip)
    //         ->response(Response::HTTP_OK);
    // }
}
