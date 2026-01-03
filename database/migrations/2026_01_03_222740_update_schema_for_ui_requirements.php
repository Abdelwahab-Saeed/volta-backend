<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->string('backup_phone_number')->nullable()->after('phone_number');
            $table->string('name')->nullable()->change();
            $table->string('address_line_1')->nullable()->change();
            $table->string('zip_code')->nullable()->change();
            $table->string('country')->nullable()->change();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_method')->default('cash')->after('status');
            $table->text('notes')->nullable()->after('payment_method');
            $table->decimal('shipping_cost', 10, 2)->default(0)->after('subtotal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropColumn('backup_phone_number');
            $table->string('name')->nullable(false)->change();
            $table->string('address_line_1')->nullable(false)->change();
            $table->string('zip_code')->nullable(false)->change();
            $table->string('country')->nullable(false)->change();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'notes', 'shipping_cost']);
        });
    }
};
