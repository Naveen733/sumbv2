<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SumbTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sumb_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->foreignId('user_id')->nullable()->index();
            $table->string('transaction_type')->default('invoice');
            $table->integer('transaction_id')->default(0);
            $table->integer('client_id')->default(0);
            $table->text('client_name')->nullable();
            $table->text('client_email')->nullable();
            $table->text('client_address')->nullable();
            $table->text('client_phone')->nullable();
            $table->double('amount', 15, 2);
            $table->string('status_paid')->default('unpaid');
            $table->text('logo')->nullable();
            $table->text('invoice_details')->nullable();
            $table->text('invoice_footer')->nullable();
            $table->text('invoice_invoice')->nullable();
            $table->date('invoice_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sumb_transactions');
    }
}
