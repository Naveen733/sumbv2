<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionCollections extends Model
{
    // use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * 
    */
    protected $table = 'transaction_collections';

    protected $fillable = ['user_id', 'invoice_ref_number', 'issue_date', 'due_date', 'transaction_number', 'transaction_type', 'client_name', 'client_email', 'client_phone', 'sub_total', 'total_gst', 'total_amount', 'default_tax', 'logo', 'invoice_sent', 'status', 'is_active'];

    public function transactions() {
        return $this->hasMany(Transactions::class, 'transaction_collection_id');
    }
}
?>