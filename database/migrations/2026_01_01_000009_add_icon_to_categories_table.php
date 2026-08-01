<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('icon')->nullable()->after('type');
        });

        // Seed default emojis for existing categories
        $defaultIcons = [
            'Gaji Utama' => '💵',
            'Bonus & Tunjangan' => '🎁',
            'Honor Kepanitiaan' => '📜',
            'Investasi & Dividen' => '📈',
            'Usaha Sampingan' => '💼',
            'Belanja Bulanan' => '🛒',
            'Biaya Admin Bank / E-Wallet' => '🏦',
            'Gaya Hidup' => '🛍️',
            'Hiburan & Rekreasi' => '🎬',
            'Internet dan Komunikasi' => '🌐',
            'Kesehatan' => '💊',
            'Kopi' => '☕',
            'Makanan & Minuman' => '🍔',
            'Pendidikan' => '🎓',
            'Tagihan & Utilitas' => '⚡',
            'Transfer Antar Rekening' => '🔄',
        ];

        foreach ($defaultIcons as $name => $icon) {
            DB::table('categories')->where('name', $name)->update(['icon' => $icon]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('icon');
        });
    }
};
