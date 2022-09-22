<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SumbClients extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sumb_clients', function (Blueprint $table) {
            $table->increments('id');
            $table->foreignId('user_id')->nullable()->index();
            $table->string('client_name');
            $table->string('client_contact_person')->nullable();
            $table->string('client_contact_number')->nullable();
            $table->text('client_address')->nullable();
            $table->text('client_comments')->nullable();
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
        Schema::dropIfExists('sumb_clients');
    }
}
