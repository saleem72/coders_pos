<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\UploadUserImage;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Requests\Api\V1\UploadUserImageRequest;

class UserController extends Controller
{
    public function uploadImage(UploadUserImageRequest $request, UploadUserImage $service) {
        $user = $request->user();
        $file = $request->file('image');
        $imageId = hexdec(uniqid());
        $fileName = $imageId.'.'.$file->getClientOriginalExtension();
        if ($user->avatar) {
            $service->deleteOld($user->avatar);
        }
        $service->upload($file, $fileName);

        $user->update([
            'avatar' => $fileName,
        ]);


        $data = [
            'success' => true,
            'message' => 'Image was uploaded successfuly.',
            'data' => NULL
        ];
        return response()->json($data, 200);
    }

    public function getUsers(Request $request) {
        $list = User::with('role')->get();
        $users = UserResource::collection($list);
        $data = [
            'success' => true,
            'message' => 'Image was uploaded successfuly.',
            'data' => $users
        ];
        return response()->json($data, 200);
    }
}
