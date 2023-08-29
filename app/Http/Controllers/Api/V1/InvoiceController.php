<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Services\InvoiceService;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\InvoiceResource;
use App\DataTransfareObjects\V1\CustomJson;
use App\Http\Requests\V1\AddInvoiceRequest;
use App\Http\Requests\V1\UpdateInvoiceRequest;
use App\Http\Resources\V1\InvoiceDetailsResource;
use App\DataTransfareObjects\V1\CustomPaginatedJson;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $invoices = Invoice::paginate();
        $modify = InvoiceResource::collection($invoices);

        $data = CustomPaginatedJson::toArray($invoices, $modify);

        return response()->json($data, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AddInvoiceRequest $request, InvoiceService $service)
    {
        $input = $request->safe()->only([
            'customer_id',
            'number',
            'invoice_date',
            'tax',
            'notes'
        ]);

        $collection = collect($request['invoice_items']);
        $invoice = $service->createInvoice($input, $collection);

        $modify = new InvoiceDetailsResource($invoice);
        $data = new CustomJson(status: true, message: 'success', data: $modify);


        return response()->json($data->toArray(), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $invoice = Invoice::findOrFail($id);
            $resource = new InvoiceDetailsResource($invoice);
            $data = new CustomJson(status: true, message: 'success', data: $resource);
        return response()->json($data->toArray(), 200);
        } catch (ModelNotFoundException $e) {
            $data = new CustomJson(status: false, message: "No query results for invoice ".$id, data: Null);
            return response()->json($data->toArray(), 404);
        }

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInvoiceRequest $request, string $id, InvoiceService $service)
    {
        try {
            $invoice = Invoice::findOrFail($id);
            $input = $request->safe()->only([
                'customer_id',
                'number',
                'invoice_date',
                'tax',
                'notes'
            ]);

            $collection = $request['invoice_items'] ? collect($request['invoice_items']) : Null;

            $invoice2 = $service->updateInvoice($invoice, $input, $collection);

            $data = new CustomJson(status: true, message: 'Success', data: new InvoiceDetailsResource($invoice));
            return response()->json($data->toArray(), 404);
        } catch (ModelNotFoundException $e) {
            $data = new CustomJson(status: false, message: "No query results for invoice ".$id, data: Null);
            return response()->json($data->toArray(), 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $invoice = Invoice::findOrFail($id);
            $invoice->delete();
            $data = new CustomJson(status: true, message: 'Invoice with id: '.$id.' has deleted successfuly.', data: Null);
            return response()->json($data->toArray(), 200);
        } catch (ModelNotFoundException $e) {
            $data = new CustomJson(status: false, message: "No query results for Customer ".$id, data: Null);
            return response()->json($data->toArray(), 404);
        }
    }

    public function invoicesForCustomer(Request $request, string $id) {
        try {
            $invoices = Customer::findOrFail($id)->invoices;
            $data = new CustomJson(status: true, message: 'success', data: InvoiceResource::collection($invoices));
            return response()->json($data->toArray(), 200);
        } catch (ModelNotFoundException $e) {
            $data = new CustomJson(status: false, message: "No query results for Customer ".$id, data: Null);
            return response()->json($data->toArray(), 404);
        }
    }

    public function invoicesForProduct(Request $request, string $id) {
        try {
            $invoices = Product::findOrFail($id)->loadMissing('invoices')->invoices;
            $data = new CustomJson(status: true, message: 'success', data: InvoiceResource::collection($invoices));
            return response()->json($data->toArray(), 200);
        } catch (ModelNotFoundException $e) {
            $data = new CustomJson(status: false, message: "No query results for Product ".$id, data: Null);
            return response()->json($data->toArray(), 404);
        }
    }
}
