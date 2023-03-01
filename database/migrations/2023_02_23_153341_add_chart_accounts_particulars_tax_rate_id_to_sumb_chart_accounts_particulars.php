<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddChartAccountsParticularsTaxRateIdToSumbChartAccountsParticulars extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sumb_chart_accounts_particulars', function (Blueprint $table) {
            $table->bigInteger('chart_accounts_particulars_tax_rate_id')->unsigned()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sumb_chart_accounts_particulars', function (Blueprint $table) {
            $table->dropColumn('chart_accounts_particulars_tax_rate_id');
        });
    }
}
