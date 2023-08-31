<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use App\DataTransfareObjects\V1\CustomJson;
use App\Http\Requests\V1\CreateUserRequest;

class SuperAdminControoler extends Controller
{
    public function createUser(CreateUserRequest $request) {
        $email = $request['email'];
        $password = $request['password'];
        $name = $request['name'];
        $role = $request->has('role') ? $request['role'] : Null;

        $role_id = Role::user;

        if ($role == 'superAdmin') {
            $role_id = Role::superAdmin;
        } else if($role == 'admin') {
            $role_id = Role::admin;
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role_id' => $role_id,
            'is_active' => true,
            'is_verified' => false
        ]);

        $user = $user->loadMissing('role');

        $resource = new UserResource($user);
        $data = new CustomJson(status: true, message: 'Success', data: $resource);
        return response()->json($data->toArray(), 201);
    }
}
