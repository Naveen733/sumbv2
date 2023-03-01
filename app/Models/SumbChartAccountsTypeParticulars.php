<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SumbChartAccountsTypeParticulars extends Model
{
    // use HasFactory;

    protected $table = 'sumb_chart_accounts_particulars';

    protected $fillable = ['user_id', 'chart_accounts_id', 'chart_accounts_particulars_tax_rate_id', 'chart_accounts_type_id', 'chart_accounts_particulars_code', 'chart_accounts_particulars_name', 'chart_accounts_particulars_description', 'chart_accounts_particulars_tax'];

    public function chartAccountsTypes() {
        return $this->belongsTo(SumbChartAccountsType::class, 'chart_accounts_type_id');
    }

    public function chartAccounts() {
        return $this->belongsTo(SumbChartAccounts::class);
    }

    public function invoicePartsDetails() {
        return $this->hasMany(SumbInvoiceParticulars::class, 'invoice_chart_accounts_parts_id');
    }

    public function invoiceTaxRates() {
        return $this->belongsTo(SumbInvoiceTaxRates::class, 'chart_accounts_particulars_tax_rate_id');
    }

    public function invoiceItems() {
        return $this->hasMany(SumbInvoiceItems::class, 'invoice_item_chart_accounts_parts_id');
    }
}