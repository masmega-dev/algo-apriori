<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $t): void {
            $t->id();
            $t->string('name');
            $t->string('phone', 20)->unique();
            $t->timestamp('last_order_at')->nullable();
            $t->timestamps();
            $t->index('name');
        });
        Schema::create('cake_sizes', fn (Blueprint $t) => $this->catalog($t, true, false, false));
        Schema::create('cake_shapes', function (Blueprint $t): void {
            $t->id();
            $t->string('name');
            $t->decimal('price_adjustment', 12, 2)->default(0);
            $t->boolean('is_active')->default(true);
            $t->unsignedSmallInteger('sort_order')->default(0);
            $t->timestamps();
        });
        Schema::create('cake_flavors', fn (Blueprint $t) => $this->catalog($t, true, true, false));
        Schema::create('additional_items', fn (Blueprint $t) => $this->catalog($t, true, false, true));
        Schema::create('store_settings', function (Blueprint $t): void {
            $t->id();
            $t->string('store_name');
            $t->string('store_phone');
            $t->string('admin_whatsapp');
            $t->text('store_address')->nullable();
            $t->string('logo_path')->nullable();
            $t->text('customer_order_template');
            $t->text('admin_order_template');
            $t->boolean('public_order_enabled')->default(true);
            $t->unsignedSmallInteger('minimum_pickup_days')->default(1);
            $t->time('opening_time');
            $t->time('closing_time');
            $t->timestamps();
        });
        Schema::create('orders', function (Blueprint $t): void {
            $t->id();
            $t->ulid('public_token')->unique();
            $t->string('order_number')->unique();
            $t->foreignId('customer_id')->constrained()->restrictOnDelete();
            $t->foreignId('cake_size_id')->constrained();
            $t->foreignId('cake_shape_id')->constrained();
            $t->foreignId('base_flavor_id')->constrained('cake_flavors');
            $t->foreignId('filling_flavor_id')->constrained('cake_flavors');
            $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $t->foreignId('completed_by')->nullable()->constrained('users')->nullOnDelete();
            $t->string('source')->index();
            $t->dateTime('ordered_at')->index();
            $t->dateTime('pickup_at')->index();
            $t->string('fulfillment_method')->default('pickup');
            $t->string('customer_name_snapshot');
            $t->string('customer_phone_snapshot', 20);
            $t->text('delivery_address')->nullable();
            $t->decimal('delivery_fee', 12, 2)->default(0);
            $t->string('cake_size_snapshot');
            $t->string('cake_shape_snapshot');
            $t->string('base_flavor_snapshot');
            $t->string('filling_flavor_snapshot');
            $t->string('cake_text')->nullable();
            $t->string('age_text')->nullable();
            $t->string('base_color')->nullable();
            $t->string('decoration_color')->nullable();
            $t->string('character_theme')->nullable();
            $t->string('reference_image_path')->nullable();
            $t->decimal('cake_price', 12, 2);
            $t->decimal('additional_items_total', 12, 2)->default(0);
            $t->decimal('grand_total', 12, 2);
            $t->string('status')->index();
            $t->text('customer_notes')->nullable();
            $t->text('admin_notes')->nullable();
            $t->dateTime('completed_at')->nullable();
            $t->timestamps();
            $t->softDeletes();
            $t->index(['status', 'pickup_at']);
        });
        Schema::create('order_additional_items', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('order_id')->constrained()->cascadeOnDelete();
            $t->foreignId('additional_item_id')->constrained()->restrictOnDelete();
            $t->string('item_name_snapshot');
            $t->string('unit_snapshot');
            $t->decimal('unit_price', 12, 2);
            $t->unsignedInteger('quantity');
            $t->decimal('subtotal', 12, 2);
            $t->timestamps();
            $t->unique(['order_id', 'additional_item_id']);
        });
        Schema::create('whatsapp_logs', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('order_id')->constrained()->cascadeOnDelete();
            $t->string('recipient_type')->index();
            $t->string('message_type');
            $t->string('recipient_name');
            $t->string('phone', 20);
            $t->string('provider');
            $t->text('message');
            $t->string('status')->index();
            $t->string('provider_message_id')->nullable();
            $t->json('provider_response')->nullable();
            $t->text('error_message')->nullable();
            $t->unsignedInteger('attempt_count')->default(0);
            $t->dateTime('sent_at')->nullable();
            $t->dateTime('failed_at')->nullable();
            $t->timestamps();
            $t->index(['order_id', 'recipient_type']);
        });
        Schema::create('order_status_histories', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('order_id')->constrained()->cascadeOnDelete();
            $t->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $t->string('previous_status')->nullable();
            $t->string('new_status');
            $t->text('notes')->nullable();
            $t->timestamp('created_at')->useCurrent();
        });
        Schema::create('apriori_analysis_runs', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('executed_by')->nullable()->constrained('users')->nullOnDelete();
            $t->date('date_from');
            $t->date('date_to');
            $t->decimal('minimum_support', 8, 6);
            $t->decimal('minimum_confidence', 8, 6);
            $t->decimal('minimum_lift', 8, 6)->default(0);
            $t->unsignedSmallInteger('maximum_itemset')->default(3);
            $t->unsignedInteger('transaction_count')->default(0);
            $t->unsignedInteger('unique_item_count')->default(0);
            $t->unsignedInteger('rule_count')->default(0);
            $t->timestamp('executed_at');
            $t->timestamps();
        });
        Schema::create('apriori_rules', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('analysis_run_id')->constrained('apriori_analysis_runs')->cascadeOnDelete();
            $t->decimal('support', 8, 6);
            $t->decimal('confidence', 8, 6);
            $t->decimal('lift', 12, 6);
            $t->unsignedInteger('support_count');
            $t->timestamps();
            $t->index('analysis_run_id');
        });
        Schema::create('apriori_rule_items', function (Blueprint $t): void {
            $t->id();
            $t->foreignId('apriori_rule_id')->constrained('apriori_rules')->cascadeOnDelete();
            $t->foreignId('additional_item_id')->constrained()->restrictOnDelete();
            $t->string('side');
            $t->timestamps();
            $t->index(['additional_item_id', 'side']);
        });
    }

    private function catalog(Blueprint $t, bool $price, bool $type, bool $unit): void
    {
        $t->id();
        $t->string('name');
        if ($type) {
            $t->string('type');
        } if ($price) {
            $t->decimal($type ? 'price_adjustment' : ($unit ? 'price' : 'base_price'), 12, 2)->default(0);
        } if ($unit) {
            $t->string('unit')->default('pcs');
        } $t->boolean('is_active')->default(true);
        $t->unsignedSmallInteger('sort_order')->default(0);
        $t->timestamps();
    }

    public function down(): void
    {
        foreach (['apriori_rule_items', 'apriori_rules', 'apriori_analysis_runs', 'order_status_histories', 'whatsapp_logs', 'order_additional_items', 'orders', 'store_settings', 'additional_items', 'cake_flavors', 'cake_shapes', 'cake_sizes', 'customers'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
