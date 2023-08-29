<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\UploadCustomerImage;
use App\DataTransfareObjects\V1\CustomJson;
use App\Http\Resources\V1\CustomerResource;
use App\Http\Requests\Api\V1\AddCustomerRequest;
use App\Http\Requests\Api\V1\UpdateCustomerRequest;
use App\DataTransfareObjects\V1\CustomPaginatedJson;
use App\Http\Requests\V1\UploadCustomerImageRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers = Customer::paginate();
        $modify = CustomerResource::collection($customers);
        $data =  CustomPaginatedJson::toArray($customers, $modify);
        return response()->json($data, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AddCustomerRequest $request, UploadCustomerImage $service)
    {
        $input = $request->safe()->only([
            'name',
            'phone',
            'address'
        ]);

        $image = $request->has('image') ? $request->file('image') : Null;

        if ($image != Null) {
            $imageId = hexdec(uniqid());
            $fileName = $imageId.'.'.$image->getClientOriginalExtension();
            $service->upload($image, $fileName);
            $input['image'] = $imageId;
            $input['image_extension'] = $image->getClientOriginalExtension();
        }


        $customer = new Customer();
        $customer->fill($input);
        $customer->save();

        $data = [
            'success' => true,
            'message' => 'Customer was created successfuly.',
            'data' => new CustomerResource($customer)
        ];
        return response()->json($data, 200);


    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $customer = Customer::findOrFail($id);
            $data = new CustomJson(
                status: true,
                message: "Success",
                data: new CustomerResource($customer)
            );
            return response()->json($data->toArray(), 200);

        } catch (ModelNotFoundException $e) {
            $data = new CustomJson(status: false, message: "No query results for customer ".$id, data: Null);
            return response()->json($data->toArray(), 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerRequest $request, string $id)
    {
        $input = $request->safe()->only([
            'name',
            'phone',
            'address'
        ]);

        try {
            $customer = Customer::findOrFail($id);
            $customer->update($input);
            $customer = Customer::findOrFail($id);
            $data = new CustomJson(
                status: true,
                message: "Success",
                data: new CustomerResource($customer)
            );
            return response()->json($data->toArray(), 200);
        } catch (ModelNotFoundException $e) {
            $data = new CustomJson(status: false, message: "No query results for customer ".$id, data: Null);
            return response()->json($data->toArray(), 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $customer = Customer::findOrFail($id);
            $customer->delete();
            $data = new CustomJson(
                status: true,
                message: "Customer with id ".$id.' has deleted.',
                data: Null
            );
            return response()->json($data->toArray(), 200);
        } catch (ModelNotFoundException $e) {
            $data = new CustomJson(status: false, message: "No query results for customer ".$id, data: Null);
            return response()->json($data->toArray(), 404);
        }
    }

    public function updateCustomerImage(UploadCustomerImageRequest $request, UploadCustomerImage $service) {
        $customerId = $request['customerId'];
        $file = $request->file('image');
        try {
            $customer = Customer::findOrFail($customerId);
            $imageId = hexdec(uniqid());

            $service->update($customer, $file, $imageId.'.'.$file->getClientOriginalExtension());

            $customer->update([
                'image' => $imageId,
                'image_extension' => $file->getClientOriginalExtension(),
            ]);

            $customer = customer::find($customerId);

            $data = [
                'success' => true,
                'message' => 'Image was uploaded successfuly.',
                'data' => new CustomerResource($customer)
            ];
            return response()->json($data, 200);
        }  catch (ModelNotFoundException $e) {
            $data = new CustomJson(false, 'Can not find Customer '.$customerId,  Null);
            return response()->json($data->toArray(), 404);
        }
    }
}
