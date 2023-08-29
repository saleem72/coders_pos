<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Unit;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\UnitResource;
use Illuminate\Database\QueryException;
use App\Http\Requests\V1\AddUnitRequest;
use App\DataTransfareObjects\V1\CustomJson;
use App\DataTransfareObjects\V1\CustomPaginatedJson;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $units = Unit::paginate();
        $modify = UnitResource::collection($units);

       $result = CustomPaginatedJson::toArray($units, $modify);
        return response()->json($result, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AddUnitRequest $request)
    {
        $name = $request['name'];

        $unit = Unit::create([
            'name' => $name
        ]);

        $data = new CustomJson(
            status: true,
            message: 'Success',
            data: new UnitResource($unit)
        );

        return response()->json($data->toArray(), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $includeProducts = false;
        if ($request->has('includeProducts')) {
            $includeProducts = $request->boolean('includeProducts');
        }

        try {
            $unit = Unit::findOrFail($id)->loadCount('products');
            if ($includeProducts) {
                $unit = $unit->loadMissing('products');
            }
            $data = new CustomJson(
                status: true,
                message: 'Success',
                data: new UnitResource($unit)
            );

            return response()->json($data->toArray(), 202);

        } catch (NotFoundHttpException $e) {
            $data = new CustomJson(false, 'No query results for Unit '.$id,  Null);
            return response()->json($data->toArray(), 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AddUnitRequest $request, int $id)
    {
        $name = $request['name'];
        error_log('I am here');
        try {
            $unit = Unit::findOrFail($id);
            $unit->update([
                'name' => $name
            ]);

            $unit = Unit::find($id)->loadCount('products');

            $data = new CustomJson(
                status: true,
                message: 'Success',
                data: new UnitResource($unit)
            );

            return response()->json($data->toArray(), 200);
        } catch (NotFoundHttpException $e) {
            $data = new CustomJson(false, 'No query results for Unit '.$id,  Null);
            return response()->json($data->toArray(), 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $unit = Unit::findOrFail($id);
            $unit->delete();

            $data = new CustomJson(
                status: true,
                message: 'Unit with id '.$id.'was deleted succefuly ',
                data: Null
            );

            return response()->json($data->toArray(), 200);
        } catch (NotFoundHttpException $e) {
            $data = new CustomJson(false, 'No query results for Unit '.$id,  Null);
            return response()->json($data->toArray(), 404);
        } catch (QueryException $e) {
            $data = new CustomJson(status: false, message: 'Can not delete Unit who it has children.', data: Null);
            return response()->json($data->toArray(), 400);
        }
    }
}
