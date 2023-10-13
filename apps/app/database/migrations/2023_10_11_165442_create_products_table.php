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
        Schema::create('products', function (Blueprint $table) {
            $table->bigInteger('code')->unique();
            $table->enum('status', ['draft', 'trash', 'published']);
            $table->timestamp('imported_t', 0);
            $table->string('url', 500)->nullable();
            $table->string('creator', 500)->nullable();
            $table->string('product_name', 500)->nullable();
            $table->string('quantity', 500)->nullable();
            $table->string('brands', 500)->nullable();
            $table->string('categories', 500)->nullable();
            $table->string('labels', 500)->nullable();
            $table->string('cities', 500)->nullable();
            $table->string('purchase_places', 500)->nullable();
            $table->string('stores', 500)->nullable();
            $table->text('ingredients_text')->nullable();
            $table->string('traces', 500)->nullable();
            $table->string('serving_size', 500)->nullable();
            $table->decimal('serving_quantity', 8, 2)->nullable();
            $table->integer('nutriscore_score')->nullable();
            $table->string('nutriscore_grade', 500)->nullable();
            $table->string('main_category', 500)->nullable();
            $table->string('image_url', 500)->nullable();
            $table->timestamp('created_t', 0)->nullable();
            $table->timestamp('last_modified_t', 0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
