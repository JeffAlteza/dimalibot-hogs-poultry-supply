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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained();
            $table->string('customer_name');
            $table->string('slug');
            $table->foreignId('feeds_id')->nullable()->constrained();
            $table->string('feeds_name');
            $table->string('feeds_type');
            $table->integer('quantity');
            $table->unsignedBigInteger('feeds_price');
            $table->string('status');
            $table->unsignedBigInteger('paid_price');
            $table->unsignedBigInteger('total');
            $table->unsignedBigInteger('profit');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
