<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRegistrationRequest;
use App\Http\Requests\UserLoginRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use App\Services\CacheService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use ApiResponseTrait;

    protected UserService $userService;
    protected CacheService $cacheService;

    public function __construct(UserService $userService, CacheService $cacheService)
    {
        $this->userService = $userService;
        $this->cacheService = $cacheService;
    }

    public function register(UserRegistrationRequest $request)
    {
        try {
            $user = $this->userService->createUser($request->validated());
            
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'User registered successfully',
                'data' => [
                    'user' => new UserResource($user),
                    'token' => $token,
                ]
            ], 201);
            
        } catch (\Exception $e) {
            return $this->errorResponse('Registration failed. Please try again.', 500);
        }
    }

    public function login(UserLoginRequest $request)
    {
        try {
            if (!Auth::attempt($request->only('email', 'password'))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials',
                ], 401);
            }

            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'user' => new UserResource($user),
                    'token' => $token,
                ]
            ]);
            
        } catch (\Exception $e) {
            return $this->errorResponse('Login failed. Please try again.', 500);
        }
    }

    public function profile(Request $request)
    {
        try {
            $user = $this->userService->getUserWithRelations($request->user()->id);
            
            return response()->json([
                'success' => true,
                'data' => new UserResource($user),
            ]);
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch profile.', 500);
        }
    }

    public function updateProfile(Request $request)
    {
        try {
            $user = $request->user();
            
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255|regex:/^[a-zA-Z\s]+$/',
                'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
                'phone_number' => 'sometimes|nullable|string|max:20|regex:/^[\+]?[1-9][\d]{0,15}$/',
                'profile_image' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                return $this->handleValidationErrors($validator);
            }

            $data = $validator->validated();
            $image = $request->file('profile_image');
            
            $updatedUser = $this->userService->updateProfile($user, $data, $image);
            
            // Clear user-related cache
            $this->cacheService->clearModelCache('User');

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'data' => new UserResource($updatedUser),
            ]);
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update profile.', 500);
        }
    }

    public function changePassword(Request $request)
    {
        try {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'current_password' => 'required|string',
                'password' => 'required|string|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return $this->handleValidationErrors($validator);
            }

            $user = $request->user();
            $this->userService->changePassword(
                $user,
                $request->current_password,
                $request->password
            );

            return response()->json([
                'success' => true,
                'message' => 'Password changed successfully',
            ]);
            
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to change password.', 500);
        }
    }

    public function forgotPassword(Request $request)
    {
        try {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'email' => 'required|email',
            ]);

            if ($validator->fails()) {
                return $this->handleValidationErrors($validator);
            }

            $status = Password::sendResetLink($request->only('email'));

            if ($status === Password::RESET_LINK_SENT) {
                return response()->json([
                    'success' => true,
                    'message' => 'Password reset link sent to your email',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Unable to send reset link',
            ], 400);
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to send reset link.', 500);
        }
    }

    public function resetPassword(Request $request)
    {
        try {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'token' => 'required|string',
                'email' => 'required|email',
                'password' => 'required|string|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return $this->handleValidationErrors($validator);
            }

            $status = Password::reset($request->only('email', 'password', 'password_confirmation', 'token'), function ($user, $password) {
                $user->forceFill([
                    'password' => \Illuminate\Support\Facades\Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            });

            if ($status === Password::PASSWORD_RESET) {
                return response()->json([
                    'success' => true,
                    'message' => 'Password reset successfully',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Unable to reset password',
            ], 400);
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to reset password.', 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully',
            ]);
            
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to logout.', 500);
        }
    }
}
