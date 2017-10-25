<?php

use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        App\Customer::insert([

                [
                    'cust_id' => 'C0001',
                    'cust_balance' => '30.00',
                ],

                [
                    'cust_id' => 'C0002',
                    'cust_balance' => '50.00',
                ],

                [
                    'cust_id' => 'C0003',
                    'cust_balance' => '30.00',
                ]

        ]);
    }
}