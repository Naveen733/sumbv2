<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SumbInvoiceParticulars extends Model
{
    // use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'sumb_invoice_particulars';

    protected $fillable = ['user_id', 'invoice_id', 'quantity', 'unit_price', 'description', 'amount', 'invoice_part_code', 'invoice_part_name', 'invoice_part_tax_rate'];

}
