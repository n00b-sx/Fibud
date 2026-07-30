<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            ['name' => 'Tunai / Cash', 'account_number' => '-', 'initial_balance' => 500000],
            ['name' => 'Bank BCA', 'account_number' => '1234567890', 'initial_balance' => 5000000],
            ['name' => 'GoPay / E-Wallet', 'account_number' => '08123456789', 'initial_balance' => 250000],
        ];

        foreach ($accounts as $acc) {
            Account::firstOrCreate(
                ['name' => $acc['name']],
                $acc
            );
        }
    }
}
