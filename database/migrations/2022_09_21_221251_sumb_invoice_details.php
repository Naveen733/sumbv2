<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SumbInvoiceDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sumb_invoice_details', function (Blueprint $table) {
            $table->increments('id');
            $table->foreignId('user_id')->nullable()->index();
            $table->string('invoice_name');
            $table->string('invoice_email')->nullable();
            $table->string('invoice_phone')->nullable();
            $table->text('invoice_desc')->nullable();
            $table->text('invoice_logo')->nullable();
            $table->text('invoice_format')->nullable();
            $table->integer('deault')->default(0);
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
        Schema::dropIfExists('sumb_invoice_details');
    }
}
