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
        Schema::create('projects_users', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('project_id')->comment('Id проекта');
            $table->unsignedBigInteger('user_id')->comment('Id пользователя, отношение с проектом: многие ко многим');

            // При обновлении и удалении родительской таблицы projects и users, изменяться будет и связующая таблица
            $table->foreign('project_id', 'pu_project_fk')->on('projects')->references('id')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('user_id', 'pu_user_fk')->on('users')->references('id')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects_users');
    }
};
