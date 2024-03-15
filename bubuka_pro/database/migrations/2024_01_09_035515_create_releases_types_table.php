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
        Schema::create('releases_types', function (Blueprint $table) {
            $table->id()->comment('Id');
            $table->string('name')->comment('Тип релиза');
            $table->text('description')->nullable()->comment('Краткое описание типа релиза');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('releases_types');
    }
};
