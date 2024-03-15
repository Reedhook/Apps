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
        Schema::create('technicals_requirements', function (Blueprint $table) {
            $table->id()->comment('Id');
            $table->string('os_type')->comment('Операционная система');
            $table->text('specifications')->comment('Прочие характеристики');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('technicals_requirements');
    }
};
