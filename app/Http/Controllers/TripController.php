<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\User;
use Illuminate\Http\Request;
use App\Response\ApiResponse;
use App\Services\TripService;
use App\Http\Resources\TripResource;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Requests\Trip\AddMemberRequest;
use App\Http\Requests\Trip\CreateTripRequest;
use App\Http\Requests\Trip\UpdateTripRequest;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TripController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;
    protected TripService $tripService;
    public function __construct(TripService $tripService)
    {
        $this->tripService =  $tripService;
    }

    public function index()
    {
        try {
            // Get all trips with owner info
            $trips = $this->tripService->listAllTrips();
            if (!$trips) {
                return ApiResponse::setMessage('No trips at present')
                    ->response(Response::HTTP_OK);
            }

            return ApiResponse::setMessage('All trips fetched successfully')
                ->setData(TripResource::collection($trips))
                ->response(Response::HTTP_OK);
        } catch (\Throwable $e) {
            return ApiResponse::setMessage('Failed to fetch trips: ' . $e->getMessage())
                ->response(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function createTrip(CreateTripRequest $request)
    {
        $trip = $this->tripService->createTrip($request->validated(), auth()->user()['id']);

        return ApiResponse::setMessage('Trip created successfully')
            ->setData($trip)
            ->response(Response::HTTP_CREATED);
    }
    public function show(Trip $trip)
    {
        // $trips = $this->tripService->listAllTrips(au);
        $trip->load('users', 'itineraries');

        return ApiResponse::setMessage('Trip details fetched')
            ->setData($trip)
            ->response(Response::HTTP_OK);
    }

    public function update(UpdateTripRequest $request, Trip $trip)
    {
        $this->authorize('checkIsOwner', $trip);

        $trip = $this->tripService->updateTrip($trip, $request->validated(), auth()->id());

        return ApiResponse::setMessage('Trip updated successfully')
            ->setData($trip)
            ->response(Response::HTTP_OK);
    }

    public function destroy(Trip $trip)
    {
        $this->authorize('checkIsOwner', $trip);
        $this->tripService->deleteTrip($trip, auth()->id());

        return ApiResponse::setMessage('Trip deleted successfully')
            ->response(Response::HTTP_OK);
    }

    public function addMember(AddMemberRequest $request, Trip $trip)
    {
        $this->authorize('checkIsOwner', $trip);

        $member = $this->tripService->addMember(
            $trip,
            $request['user_id'],
            $request['role'] ?? 'traveler'
        );

        return ApiResponse::setMessage('Member added successfully')
            ->setData($member)
            ->response(Response::HTTP_CREATED);
    }

    public function removeMember(Trip $trip, $user)
    {
        $this->authorize('checkIsOwner', $trip);
        // $this->authorize('checkIsNotOwner', $trip);
        $this->tripService->removeMember($trip, $user);
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
