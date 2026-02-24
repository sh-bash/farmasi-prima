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
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'category_id')) {
                $table->foreignId('category_id')
                    ->nullable()
                    ->after('id')
                    ->constrained()
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('products', 'form_id')) {
                $table->foreignId('form_id')
                    ->nullable()
                    ->after('category_id')
                    ->constrained()
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('products', 'is_generic')) {
                $table->boolean('is_generic')->default(false);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['form_id']);

            $table->dropColumn(['category_id', 'form_id', 'is_generic']);
        });
    }
};
