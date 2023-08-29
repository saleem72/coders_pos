<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Services\AppPaginator;
use App\Services\ProductService;
use App\Http\Controllers\Controller;
use App\Services\UploadProductImage;
use Illuminate\Database\QueryException;
use App\Http\Resources\V1\ProductResource;
use App\DataTransfareObjects\V1\CustomJson;
use App\Http\Requests\V1\AddProductRequest;
use App\Http\Requests\V1\ProductsByCategory;
use App\Http\Requests\V1\UpdateProductRequest;
use App\Http\Requests\V1\UploadProductImageRequest;
use App\DataTransfareObjects\V1\CustomPaginatedJson;
use App\Http\Requests\Api\V1\UploadUserImageRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::paginate();
        $modify = ProductResource::collection($products);
        $results = CustomPaginatedJson::toArray($products, $modify);
        return response()->json($results, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AddProductRequest $request, ProductService $service)
    {
        $input = $request->only([
            'category_id',
            'unit_id',
            'name',
            'purchase',
            'retail',
            'quantity',
            'barcode',
        ]);

        $image = $request->has('image') ? $request->file('image') : Null;

        $product = $service->createProduct($input, $image);

        $data = new CustomJson(
            status: true,
            message: 'Product was created Successfully.',
            data: $product
        );

        return response()->json($data->toArray(), 201);

        // InvalidArgumentException
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $product = Product::findOrFail($id)->loadMissing(['category', 'unit']);
            $data = new CustomJson(
                status: false,
                message: 'Success.',
                data: new ProductResource($product)
            );
            return response()->json($data->toArray(), 200);
        } catch (QueryException $e) {
            $data = new CustomJson(
                status: false,
                message: 'Can not delete Product who it has children.',
                data: Null
            );
            return response()->json($data->toArray(), 400);
        } catch (NotFoundHttpException $e) {
            $data = new CustomJson(false, 'Can not find Product '.$id,  Null);
            return response()->json($data->toArray(), $e->getStatusCode());
        } catch (ModelNotFoundException $e) {
            $data = new CustomJson(false, 'Can not find Product '.$id,  Null);
            return response()->json($data->toArray(), 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, string $id, ProductService $service)
    {

        $image = $request->has('image') ? $request->file('image') : Null;
        // return response()->json(['image' => $image]);
        $input = $request->safe()
        ->only([
            'category_id',
            'unit_id',
            'name',
            'purchase',
            'retail',
            'quantity',
            'barcode',
        ]);

        try {
            $product = Product::findOrFail($id);
            $product = $service->updateProduct($product, $input, $image);

            $data = new CustomJson(
                status: true,
                message: 'Success',
                data: new ProductResource($product)
            );

            return response()->json($data->toArray(), 200);
        } catch (ModelNotFoundException $e) {
            $data = new CustomJson(false, "No query results for model Product ".$id,  Null);
            return response()->json($data->toArray(), 422);
        }
        catch (\Throwable $e) {
            $data = new CustomJson(
                status: false,
                message: 'You have to implement this '.get_class($e),
                data: [
                    'input' => $input,
                    'file' => $image, // == Null ? Null : $image->getClientOriginalExtension(),
                    'data' => Null
                ]
            );

            return response()->json($data->toArray(), 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->delete();
            $data = new CustomJson(
                status: true,
                message: 'Product with id '.$id.' had successfuly deleted',
                data: Null
            );

            return response()->json($data->toArray(), 200);
        } catch (ModelNotFoundException $e) {
            $data = new CustomJson(false, "No query results for model Product ".$id,  Null);
            return response()->json($data->toArray(), 404);
        }
        catch (\Throwable $e) {
            $data = new CustomJson(
                status: false,
                message: 'something went wrong '.get_class($e),
                data: Null
            );

            return response()->json($data->toArray(), 400);
        }
    }

    /**
     * Display a listing of the products belong to category.
     */
    public function productsByCategory(ProductsByCategory $request)
    {
        $categoryId = $request['categoryId'];
        $page = $request->has('page') ? $request['page'] : 1;
        $perPage = $request->has('perPage') ? $request['perPage'] : 7;

        $category = Category::where('id', $categoryId)->first();
        $category = $category->loadMissing(['children_products', 'products', 'children']);
        $temp = $category->products->merge($category->children_products);


        $products = AppPaginator::paginate($temp, $perPage, $page);
        $modify = ProductResource::collection($products);
        $results = CustomPaginatedJson::toArray($products, $modify);
        return response()->json($results, 200);
    }

    public function updateProductImage(UploadProductImageRequest $request, ProductService $service) {
        $productId = $request['productId'];
        $file = $request->file('image');
        try {
            $product = Product::findOrFail($productId);
            $imageId = hexdec(uniqid());

            $service->uploadImage($product, $file, $imageId);

            $product->update([
                'image' => $imageId,
                'image_extension' => $file->getClientOriginalExtension(),
            ]);

            $product = Product::find($productId)->loadMissing(['category', 'unit']);

            $data = [
                'success' => true,
                'message' => 'Image was uploaded successfuly.',
                'data' => new ProductResource($product)
            ];
            return response()->json($data, 200);
        }  catch (NotFoundHttpException $e) {
            $data = new CustomJson(false, 'Can not find Product '.$productId,  Null);
            return response()->json($data->toArray(), $e->getStatusCode());
        }
    }
}


