<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'number',
        'invoice_date',
        'tax',
        'notes',
        'subtotal'
    ];

    public function customer() {
        return $this->belongsTo(Customer::class);
    }

    public function invoiceItems() {
        return $this->hasMany(InvoiceItem::class);
    }

    public function setInvoiceDateAttribute($value) {

        // $this->attributes['invoice_date'] = Carbon::parse($value)
        //             // ->copy()
        //             // ->tz(Auth::user()->timezone)
        //             ->format('Y-m-d');

        if(Auth::check())
            {
                $this->attributes['invoice_date'] = Carbon::createFromFormat('d-m-Y H:i:s', $value)
                    ->copy()
                    ->tz(Auth::user()->timezone)
                    ->format('Y-m-d H:i:s');
            }
        else
            {
                $this->attributes['invoice_date'] = Carbon::createFromFormat('d-m-Y H:i:s', $value)
                ->copy()
                ->tz('America/Toronto')
                ->format('Y-m-d D H:i:s');
            }
    }

    public function getInvoiceDateAttribute($date)
    {
        if(Auth::check())
            {
                return Carbon::parse($date)->copy()->tz(Auth::user()->timezone)->format('Y-m-d D H:i:s');
            }
        else
            {
                return Carbon::parse($date)->copy()->tz('America/Toronto')->format('Y-m-d H:i:s');
            }
    }
}
