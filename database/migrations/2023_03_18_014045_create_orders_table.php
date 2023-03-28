<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignUuid('user_uuid')->constrained('users', 'uuid')->cascadeOnDelete();
            $table->foreignUuid('order_status_uuid')->constrained('order_statuses', 'uuid')->nullOnDelete();
            $table->foreignUuid('payment_uuid')->constrained('payments', 'uuid')->cascadeOnDelete();
            $table->string('uuid')->unique();
            $table->json('products');
            $table->json('address');
            $table->float('delivery_fee')->nullable();
            $table->float('amount', 12);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate();
            $table->timestamp('shipped_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
