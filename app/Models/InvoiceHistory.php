<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceHistory extends Model
{
    // use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     *
     */
    protected $table = 'invoice_history';

    public $timestamps = false;

    protected $fillable = ['user_id', 'invoice_id', 'user_name', 'invoice_number', 'action', 'description', 'date', 'time'];
}
?>