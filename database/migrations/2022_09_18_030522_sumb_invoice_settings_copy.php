<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SumbInvoiceSettingsCopy extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sumb_invoice_settings_copy', function (Blueprint $table) {
            $table->increments('id');
            $table->foreignId('user_id')->nullable()->index();
            $table->integer('invoice_count')->default(1);
            $table->integer('expenses_count')->default(1);
            $table->text('logo')->nullable();
            $table->integer('invoice_format')->default(0);
            $table->text('invoice_name')->nullable();
            $table->text('invoice_email')->nullable();
            $table->text('invoice_phone')->nullable();
            $table->text('invoice_details')->nullable();
            $table->text('invoice_footer')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sumb_invoice_settings_copy');
    }
}
