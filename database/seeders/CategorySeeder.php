<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // Income
            ['name' => 'Gaji Utama', 'type' => 'income'],
            ['name' => 'Bonus & Tunjangan', 'type' => 'income'],
            ['name' => 'Investasi & Dividen', 'type' => 'income'],
            ['name' => 'Usaha Sampingan', 'type' => 'income'],
            // Expense
            ['name' => 'Makanan & Minuman', 'type' => 'expense'],
            ['name' => 'Belanja Bulanan', 'type' => 'expense'],
            ['name' => 'Tagihan & Utilitas', 'type' => 'expense'],
            ['name' => 'Transportasi', 'type' => 'expense'],
            ['name' => 'Hiburan & Rekreasi', 'type' => 'expense'],
            ['name' => 'Kesehatan', 'type' => 'expense'],
            ['name' => 'Pendidikan', 'type' => 'expense'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(
                ['name' => $cat['name'], 'type' => $cat['type']]
            );
        }
    }
}
