<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Enums\User\UserRole;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\User\UserResource;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\VerifyEmailRequest;
use Illuminate\Auth\Notifications\VerifyEmail;
use App\Http\Requests\Auth\ReSendVerificationCodeRequest;

class AuthController extends Controller
{
    use ApiResponseTrait;

    //public function __construct(protected ServicesFormatPhoneNumber $phoneFormatter) {}

    public function register(RegisterRequest $request)
    {
        try {
            return DB::transaction(function () use ($request) {
                $data = $request->validated();

                $path = null;
                if (isset($data['avatar'])) {
                    $path = $data['avatar']->storeAs('profile/images', 'profile_' . time() . '.' . $data['avatar']->getClientOriginalExtension(), 'public');
                }

                $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'avatar' => $path,
                    'role' => UserRole::USER,
                    'password' => Hash::make($data['password']),
                ]);

                $resource = UserResource::make($user);

                return $this->successResponse(
                    data: $resource,
                    message: __('User created successfully. Please check your email for verification.'),
                    code: 201
                );
            });
        } catch (\Exception $e) {
            Log::error('User registration failed: ' . $e->getMessage());
            return $this->errorResponse(__('Registration failed. Please try again.'));
        }
    }

    public function reSendVerificationCode(ReSendVerificationCodeRequest $request)
    {
        try {
            $data = $request->validated();

            $user = User::where('email', $data['email'])->first();

            if (!$user) {
                return $this->notFoundResponse(__('User not found'));
            }

            if ($user->hasVerifiedEmail()) {
                return $this->errorResponse(__('Email is already verified'), 400);
            }

            $user->generateAndSendVerificationCode();

            return $this->successResponse(
                message: __('Verification code sent successfully. Please check your email.')
            );
        } catch (\Exception $e) {
            Log::error('Resend verification failed: ' . $e->getMessage());
            return $this->errorResponse(__('Failed to send verification code'));
        }
    }

    public function verifyEmail(VerifyEmailRequest $request)
    {
        try {
            return DB::transaction(function () use ($request) {
                $data = $request->validated();

                $user = User::where('email', $data['email'])->first();

                if (!$user) {
                    return $this->notFoundResponse(__('User not found'));
                }

                if ($user->hasVerifiedEmail()) {
                    return $this->errorResponse(__('Email is already verified'), 400);
                }

                if ($user->verify_otp !== $data['verification_code']) {
                    return $this->errorResponse(__('Invalid verification code'), 422);
                }

                if (now()->greaterThan($user->email_otp_expires_at)) {
                    return $this->errorResponse(__('OTP expired. Please request a new one.'), 410);
                }

                $user->markEmailAsVerified();
                $user->verify_otp = null;
                $user->email_otp_expires_at = null;
                // $user->status = UserStatus::ACTIVE;
                $user->save();

                return $this->successResponse(
                    data: UserResource::make($user),
                    message: __('Email verified successfully')
                );
            });
        } catch (\Exception $e) {
            Log::error('Email verification failed: ' . $e->getMessage());
            return $this->errorResponse(__('Email verification failed'));
        }
    }



    public function login(LoginRequest $request)
    {
        try {
            $data = $request->validated();

            $user = User::where('email', $data['email'])->first();

            if (!$user) {
                return $this->notFoundResponse(__('User not found'));
            }
            if (!Hash::check($data['password'], $user->password)) {
                return $this->errorResponse(__('Invalid credentials'), 401);
            }


            // if ($user->status !== UserStatus::ACTIVE) {
            //     return $this->errorResponse(__('User account is not active'), 403);
            // }

            if (!$user->hasVerifiedEmail()) {
                return $this->errorResponse(message: __('Please verify your email address before logging in.'), code: 403);
            }

            $tokenName = 'auth_token_' . now()->timestamp;
            //$user->tokens()->delete();
            $token = $user->createToken($tokenName)->plainTextToken;


            Log::info('User logged in successfully', [
                'user_id' => $user->id,
                'tokenName' => $tokenName,
                'request_ip' => request()->ip()
            ]);

            return $this->successResponse(
                data: [
                    'user' => UserResource::make($user),
                    'token' => $token,
                ],
                message: __('Login successful'),
                code: 200
            );
        } catch (\Exception $e) {
            Log::error('Login failed: ' . $e->getMessage(), [
                'request_ip' => request()->ip()
            ]);
            return $this->errorResponse(__('Login failed. Please try again.'));
        }
    }

    public function logout(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return $this->errorResponse(__('User not authenticated'), 401);
            }

            $user->currentAccessToken()->delete();

            Log::info('User logged out successfully', ['user_id' => $user->id]);

            return $this->successResponse(message: __('Logged out successfully'));
        } catch (\Exception $e) {
            Log::error('Logout failed: ' . $e->getMessage());
            return $this->errorResponse(__('Logout failed. Please try again.'));
        }
    }
}
