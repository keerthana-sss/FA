<?php

namespace App\Services;

use App\Models\User;
use Nyholm\Psr7\Response;
use Illuminate\Http\Request;
use App\Response\ApiResponse;
use InvalidArgumentException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Nyholm\Psr7\Factory\Psr17Factory;
use Illuminate\Support\Facades\Password;
use App\Contracts\UserRepositoryInterface;
use App\Exceptions\ResetCodeException;
use App\Exceptions\ResourceNotFoundException;
use Illuminate\Validation\ValidationException;
use Laravel\Passport\Http\Controllers\AccessTokenController;

class AuthService
{
    protected UserRepositoryInterface $userRepository;
    protected $psr17Factory;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
        $this->psr17Factory = new Psr17Factory();
    }

    /**
     * Register a new user and return access token
     */
    public function register(array $data)
    {
        $user =  User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => $data['password'],
        ]);

        return [$user, $this->issueToken($data['email'],  $data['password'])];
        // return $data;
    }

    /**
     * Login and issue a personal access token
     */
    public function login(array $data)
    {
        $user = $this->userRepository->findByEmail($data['email']);

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'],
            ]);
        }

        if ($user->is_blocked ?? false) {
            throw ValidationException::withMessages([
                'email' => ['Your account is blocked.'],
            ]);
        }

        return [$user, $this->issueToken($data['email'],  $data['password'])];
    }

    /**
     * Logout (revoke all tokens)
     */
    public function logout($user)
    {
        // If using the token guard, the token is available via $request->user()->token()
        // but with Passport and `auth:api`, you can get token id from the bearer token


        $accessToken = $user->token();


        if ($accessToken) {
            // Revoke access token
            $accessToken->revoke();


            // Revoke associated refresh tokens
            DB::table('oauth_refresh_tokens')
                ->where('access_token_id', $accessToken->id)
                ->update(['revoked' => true]);
        }


        // return response()->json(['message' => 'Logged out successfully']);
    }

    /**
     * Create a PSR-7 ServerRequest and call Passport's AccessTokenController->issueToken()
     * to get access & refresh token response. We must supply the password client id and secret.
     */
    protected function issueToken(string $username, string $password)
    {
        info('in issuetoken');
        $clientId = env('PASSPORT_PASSWORD_CLIENT_ID');
        $clientSecret = env('PASSPORT_PASSWORD_CLIENT_SECRET');


        if (!$clientId || !$clientSecret) {
            return response()->json(['error' => 'OAuth password client not configured. Run `php artisan passport:client --password` and add PASSPORT_PASSWORD_CLIENT_ID & _SECRET to .env'], 500);
        }


        // Build form parameters similar to an HTTP POST to /oauth/token
        $params = [
            'grant_type' => 'password',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'username' => $username,
            'password' => $password,
            'scope' => '',
        ];


        // Create a PSR-7 ServerRequest using Nyholm
        $psrFactory = $this->psr17Factory;


        // Build URI and server parameters (you may want to set host, scheme etc.)
        $uri = $psrFactory->createUri('/oauth/token');


        // create server request
        $serverRequest = $psrFactory->createServerRequest('POST', $uri, []);


        // add parsed body (form parameters)
        $serverRequest = $serverRequest->withParsedBody($params);


        // Forward to Passport AccessTokenController
        $accessTokenController = app(AccessTokenController::class);


        // AccessTokenController->issueToken() accepts a Psr ServerRequestInterface
        $psrResponse = $accessTokenController->issueToken($serverRequest, new Response());

        return $psrResponse;
    }

    public function refreshToken(Request $request)
    {
        $clientId = env('PASSPORT_PASSWORD_CLIENT_ID');
        $clientSecret = env('PASSPORT_PASSWORD_CLIENT_SECRET');


        $params = [
            'grant_type' => 'refresh_token',
            'refresh_token' => $request->refresh_token,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'scope' => '',
        ];


        $psrFactory = $this->psr17Factory;
        $uri = $psrFactory->createUri('/oauth/token');
        $serverRequest = $psrFactory->createServerRequest('POST', $uri, []);
        $serverRequest = $serverRequest->withParsedBody($params);


        $accessTokenController = app(AccessTokenController::class);
        $psrResponse = $accessTokenController->issueToken($serverRequest, new Response());

        return $psrResponse;
    }

    // public function forgotPassword($email)
    // {
    //     $user = User::whereEmail($email)->first();
    //     if (!$user) {
    //         throw new ResourceNotFoundException('User not found.');
    //     }

    //     $code = rand(100000, 999999);

    //     Cache::put('password_reset_' . $user->email, $code, now()->addMinutes(10));

    //     Mail::raw("Your password reset code is: {$code}", function ($msg) use ($user) {
    //         $msg->to($user->email)->subject('Password Reset Code');
    //     });
    // }

    // public function resetPassword($request)
    // {
    //     $cachedCode = Cache::get('password_reset_' . $request->email);

    //     if (!$cachedCode) {
    //         throw new ResetCodeException('Reset code expired or not found', 400);
    //     }

    //     if ($cachedCode != $request->code) {
    //         throw new ResetCodeException('Invalid reset code', 400);
    //     }

    //     $user = User::where('email', $request->email)->first();

    //     if (!$user) {
    //         throw new ResourceNotFoundException('User not found.');
    //     }

    //     $user->update(['password' => $request->password]);

    //     Cache::forget('password_reset_' . $request->email);
    // }
}
