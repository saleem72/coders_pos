<?php

namespace App\Http\Controllers\Auth;

use App\DataTransfareObjects\V1\CustomJson;
use Otp;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Notifications\Auth\VerifyEmailNotification;
use App\Http\Requests\Auth\EmailVerificationRequest;
use App\Http\Requests\Auth\ResetPasswordTokenRequest;
use App\Notifications\Auth\ResetPasswordNotification;

class AuthController extends Controller
{
    private $otp;

    /**
     * Create a new AuthController instance.
     */
    public function __construct() {
        $this->otp = new Otp;
    }

    public function register(RegisterRequest $request) {
        $email = $request['email'];
        $password = $request['password'];
        $name = $request['name'];


        User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role_id' => Role::user,
            'is_active' => true,
            'is_verified' => false
        ]);

        $flag = Auth::attempt(['email' => $email, 'password' => $password]);
        if ($flag) {
            $user = Auth::user();
            $user = $user->loadMissing('role');
            $token = $user->createToken('user_token', ['create', 'update'])->plainTextToken;
            $user->notify(new VerifyEmailNotification());
            return response()->json([
                'success' => true,
                'message' => 'You have registerd successfully',
                'data' => [
                    'user' => new UserResource($user),
                    'token' => $token
                ]
            ], 201);
        }

        return response()->json([
            'success' => false,
            'message' => 'Fail to register',
            'data' => NULL
        ], 500);
    }

    public function email_verification(EmailVerificationRequest $request) {
        $email = $request['email'];
        $code = $request['code'];

        $validation = $this->otp->validate($email, $code);
        if (!$validation->status) {
            // return fail message
            return response()->json([
                'success' => false,
                'message' => 'Invalid code',
                'data' => NULL
            ], 400);
        }

        // update user
        $user = User::where('email', $email)->first();
        $user->update([
            'email_verified_at' => now(),
            'is_verified' => true
        ]);
        $user->loadMissing('role');
        $token = $user->createToken('user_token',['create', 'update']);

        // return success message
        return response()->json([
            'success' => true,
            'message' => 'Activation success',
            'data' => [
                'user' => new UserResource($user),
                'token' => $token->plainTextToken
            ]
        ], 200);


    }

    public function resend_email_verification_code(ResetPasswordTokenRequest $request) {
        $email = $request['email'];
        $user = User::where('email', $email)->first();
        $user->notify(new VerifyEmailNotification());
        return response()->json([
            'success' => true,
            'message' => 'New email was sent to you with new code',
            'data' => NULL
        ], 200);
    }

    public function request_password_reset(ResetPasswordTokenRequest $request) {
        $email = $request['email'];
        $user = User::where('email', $email)->first();

        try {
            $user->notify(new ResetPasswordNotification());
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send email, please try again later',
                'data' => NULL
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Email was sent with your verivication code',
            'data' => NULL
        ], 200);
    }

    public function reset_password(ResetPasswordRequest $request) {
        $email = $request['email'];
        $password = $request['password'];
        $code = $request['code'];

        $code_validation = $this->otp->validate($email, $code);

        if (!$code_validation->status) {
            // return fail message
            return response()->json([
                'success' => false,
                'message' => 'Invalid code',
                'data' => NULL
            ], 400);
        }

        $user = User::where('email', $email)->first();

        $user->update([
            'password' => Hash::make($password)
        ]);



        return response()->json([
            'success' => true,
            'message' => 'Password has been changed successfuly, please try to login',
            'data' => NULL
        ], 200);
    }

    public function login(LoginRequest $request) {
        $email = $request['email'];
        $password = $request['password'];

        $flag = Auth::attempt(['email' => $email, 'password' => $password]);
        if ($flag) {
            $user = Auth::user();
            $user = $user->loadMissing('role');
            $roles = ['create'];
            $tokenName = 'basicToken';

            if ($user->role_id == 2) {
                $roles = ['create', 'update'];
                $tokenName = 'updateToken';
            }
            if ($user->role_id == 3) {
                $roles = ['create', 'update', 'delete'];
                $tokenName = 'adminToken';
            }
            $token = $user->createToken($tokenName, $roles)->plainTextToken;

            $date = new CustomJson(status: true, message: 'You have loggrd in successfully', data: [
                'user' => new UserResource($user),
                'token' => $token
            ]);
            // return response()->json([
            //     'status' => true,
            //     'message' => 'You have loggrd in successfully',
                // 'data' => [
                //     'user' => new UserResource($user),
                //     'token' => $token
                // ]
            // ], 200);

            return response()->json($date->toArray(), 200);
        }
        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials',
            'data' => NULL
        ], 401);
    }

    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'status' => true,
            'message' => 'you have logged out successfuly.',
            'data' => NULL
        ]);
    }
}
