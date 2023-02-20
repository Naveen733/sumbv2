<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Eloquent;

class SumbInvoiceParticulars extends Model 
{
    // use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'sumb_invoice_particulars';

    protected $fillable = ['user_id', 'invoice_id', 'invoice_parts_quantity', 'invoice_parts_unit_price', 'invoice_parts_description', 'invoice_parts_amount', 'invoice_parts_code', 'invoice_parts_name', 'invoice_parts_tax_rate'];

    public function invoice() {
        return $this->belongsTo(SumbInvoiceDetails::class);
    }
}
