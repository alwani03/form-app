<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_menus', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index('master_menus_index_name');
            $table->string('icon')->nullable();
            $table->integer('ordering')->default(0);
            $table->integer('is_active')->default(1)->index('master_menus_index_is_active');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_menus');
    }
};
