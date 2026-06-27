<?php

declare(strict_types=1);

use App\Models\StoreSetting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('store_settings', 'completed_order_template')) {
            Schema::table('store_settings', function (Blueprint $table): void {
                $table->text('completed_order_template')->nullable()->after('admin_order_template');
            });
        }

        DB::table('store_settings')
            ->whereNull('completed_order_template')
            ->update([
                'completed_order_template' => StoreSetting::defaults()['completed_order_template'],
            ]);
    }

    public function down(): void
    {
        if (! Schema::hasColumn('store_settings', 'completed_order_template')) {
            return;
        }

        Schema::table('store_settings', function (Blueprint $table): void {
            $table->dropColumn('completed_order_template');
        });
    }
};
