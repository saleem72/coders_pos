<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class InvoiceService {
    public function createInvoice($input, $collection): Invoice {
       $something = DB::transaction(function() use($input, $collection) {
            $subtotal = $collection->sum(function($item) {
                return $item['quantity'] * $item['price'];
            });

            $invoice = Invoice::create([
                'customer_id' => $input['customer_id'],
                'number' => $input['number'],
                'invoice_date' => $input['invoice_date'],
                'tax' => $input['tax'],
                'notes' => $input['notes'],
                'subtotal' => $subtotal
            ]);
            $invoiceId = $invoice->id;
            $items = $collection->map(function ($item) use ($invoiceId) {

                $item['invoice_id'] = $invoiceId;

                return $item;

            })->map(function($arr, $key) {
                return Arr::except($arr , ['productId']);
            });

            InvoiceItem::insert($items->toArray());

            return $invoice;
        });

        return $something;
    }

    public function updateInvoice(Invoice $invoice, $input, $collection) {

        $invoice = DB::transaction(function () use($invoice, $input, $collection) {

            if($collection) {

                InvoiceItem::where('invoice_id', $invoice->id)->delete();


                $invoiceId = $invoice->id;
                $items = $collection->map(function ($item) use ($invoiceId) {

                    $item['invoice_id'] = $invoiceId;

                    return $item;

                })->map(function($arr, $key) {
                    return Arr::except($arr , ['productId']);
                });

                InvoiceItem::insert($items->toArray());
            }

            // TODO: update real invoice
            if ($collection) {
                $subtotal = $collection->sum(function($item) {
                    return $item['quantity'] * $item['price'];
                });
                $input['subtotal'] = $subtotal;
            }

            $invoice->update($input);

            $invoice = Invoice::find($invoice->id);

            return $invoice;
        });

        return $invoice;
    }
}
