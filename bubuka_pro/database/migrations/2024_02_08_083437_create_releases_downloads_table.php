<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Лог скачивания релиза.
     */
    public function up(): void
    {
        Schema::create('releases_downloads', function (Blueprint $table) {
            $table->id()->comment('Id');
            $table->unsignedSmallInteger('release_id')->comment('Id релиза');
            $table->string('ip')->comment('ip адрес пользователя');
            $table->jsonb('user_agent')->comment('Агент пользователя');
            $table->string('utm')->comment('Спец. метки');
            $table->timestamps();
            $table->softDeletes()->comment('Дата мягкого удаления');

            $table->foreign('release_id')->references('id')->on('releases');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('releases_downloads');
    }
};
