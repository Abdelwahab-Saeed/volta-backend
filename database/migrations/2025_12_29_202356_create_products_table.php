<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->foreignId('category_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->string('name');
            $table->text('description')->nullable();

            $table->decimal('price', 10, 2);
            $table->decimal('discount', 5, 2)->nullable(); // percentage (0–100)

            $table->integer('stock')->default(0);
            $table->string('image')->nullable();

            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

