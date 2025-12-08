<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Response\ApiResponse;
use App\Services\AuthService;
use Illuminate\Support\Facades\DB;
use Nyholm\Psr7\Factory\Psr17Factory;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    protected $psr17Factory;
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
        $this->psr17Factory = new Psr17Factory();
    }

    
    //REGISTER
    public function register(RegisterRequest $request)
    {
        $data = $this->authService->register($request->validated());
        $token = json_decode($data[1]->getContent(), true);
        $response =  [
            'user' => $data[0],
            'token' => $token
        ];

        return ApiResponse::setMessage('Registration successful.')
            ->setData($response)
            ->response(201);
    }

    //LOGIN
    public function login(LoginRequest $request)
    {
        $data = $this->authService->login($request->validated());
        $token = json_decode($data[1]->getContent(), true);
        $response =  [
            'user' => $data[0],
            'token' => $token
        ];

        return ApiResponse::setMessage('Login successful.')
            ->setData($response)
            ->response(Response::HTTP_OK);
    }


    //LOGOUT
    public function logout(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return ApiResponse::setMessage('Unauthenticated.')
                ->response(Response::HTTP_UNAUTHORIZED);
        }

        $this->authService->logout($user);

        return ApiResponse::setMessage('Logged out successfully.')
            ->response(Response::HTTP_OK);
    }
}
