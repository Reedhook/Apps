<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('projects_platforms', function (Blueprint $table) {
            $table->id()->comment('Id');

            $table->unsignedBigInteger('project_id')->comment('Id проекта');
            $table->unsignedBigInteger('platform_id')->comment('Id платформы, отношение с проектом: многие ко многим');

            // При обновлении и удалении родительской таблицы projects и platforms, изменяться будет и связующая таблица
            $table->foreign('project_id', 'pp_project_fk')->on('projects')->references('id')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('platform_id', 'pp_platform_fk')->on('platforms')->references('id')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects_platforms');
    }
};
