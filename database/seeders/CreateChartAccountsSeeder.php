<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CreateChartAccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('sumb_chart_accounts')->delete();
        
        \DB::table('sumb_chart_accounts')->insert(array (
            0 => 
            array (
                'user_id' => 1,
                'chart_accounts_name' => 'Assets'
            ),
            1 => 
            array (
                'user_id' => 1,
                'chart_accounts_name' => 'Liabilities',
            ),
            2 => 
            array (
                'user_id' => 1,
                'chart_accounts_name' => 'Expenses',
            ),
            3 => 
            array (
                'user_id' => 1,
                'chart_accounts_name' => 'Revenue',
            ),
        ));
    }
}
