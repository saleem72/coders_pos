<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use App\Services\CategoriesWithProducts;
use App\Http\Resources\V1\CategoryCounts;
use App\DataTransfareObjects\V1\CustomJson;
use App\Http\Resources\V1\CategoryResource;
use App\Http\Requests\V1\AddCategoryRequest;
use App\Services\Categories\CategoryService;
use App\Http\Requests\V1\UpdateCategoryRequest;
use App\Services\Categories\CategoryValidateParentId;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, CategoryService $service)
    {

        $includeProducts = false;
        if ($request->has('includeProducts')) {
            $includeProducts = $request->boolean('includeProducts');
        }

        $includeChildren = false;
        if ($request->has('includeChildren')) {
            $includeChildren = $request->boolean('includeChildren');
        }

        $categories = $service->getAllCategories($includeChildren, $includeProducts);
        $modify = CategoryCounts::collection($categories);


        $data = new CustomJson(
            status: true,
            message: 'Success',
            data: $modify
        );

        return response()->json($data->toArray(), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AddCategoryRequest $request, CategoryValidateParentId $validator)
    {
        //

        $name = $request['name'];
        $parentId = $request['parent_id'] ?? Null;

        if($parentId) {
            if (!$validator->validate($parentId)) {
                $data = new CustomJson(
                    status: false,
                    message: 'Sub category can not have chidlren',
                    data: Null
                );
                return response()->json($data->toArray(), 400);
            }
        }

        $category = Category::create([
            'name' => $name,
            'parent_id' => $parentId
        ]);

        $data = new CustomJson(
            status: true,
            message: 'Success',
            data: $category
        );

        return response()->json($data->toArray(), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, int $id, CategoryService $service)
    {
        $includeProducts = false;
        if ($request->has('includeProducts')) {
            $includeProducts = $request->boolean('includeProducts');
        }

        $includeChildren = false;
        if ($request->has('includeChildren')) {
            $includeChildren = $request->boolean('includeChildren');
        }

        try {
            $category = $service->getSingleCategory($id, $includeChildren, $includeProducts);
            $modify = new CategoryCounts($category);
            $data = new CustomJson(true, '',  $modify);
            return response()->json($data->toArray(), 200);
        } catch (NotFoundHttpException $e) {
            $data = new CustomJson(false, $e->getMessage(),  Null);
            return response()->json($data->toArray(), $e->getStatusCode());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category, CategoryValidateParentId $validator)
    {

        $name = $request['name'];
        $parentId = $request['parent_id'] ?? Null;

        if($request->has('parent_id')) {
            if (!$validator->validate($parentId)) {
                $data = new CustomJson(
                    status: false,
                    message: 'Sub category can not have chidlren',
                    data: Null
                );
                return response()->json($data->toArray(), 400);
            }
            $category->update([
                'parent_id' => $parentId,
            ]);
        }
        // FIXME: parent id should be updated only when it was given in request
        if($request->has('name')) {
            $category->update([
                'name' => $name,
            ]);
        }


        $category = Category::find($category->id);

        $data = new CustomJson(
            status: true,
            message: 'Success',
            data: $category
        );

        return response()->json($data->toArray(), 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {

        try {
            $category = Category::findOrFail($id);
            $category->delete();
            $data = new CustomJson(status: true, message: 'category was deleted', data: Null);
            return response()->json($data->toArray(), 400);
        } catch (QueryException $e) {
            $data = new CustomJson(status: false, message: 'Can not delete category who it has children.', data: Null);
            return response()->json($data->toArray(), 400);
        } catch (NotFoundHttpException $e) {
            $data = new CustomJson(false, 'Can not find category '.$id,  Null);
            return response()->json($data->toArray(), $e->getStatusCode());
        }

    }
}
