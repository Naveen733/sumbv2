<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SumbTransactionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('sumb_transactions')->delete();
        
        \DB::table('sumb_transactions')->insert(array (
            
        ));
        
        
    }
}