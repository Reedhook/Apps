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
        Schema::create('releases', function (Blueprint $table) {
            $table->id()->comment('Id релиза');
            $table->unsignedBigInteger('project_id')->comment('Id проекта');
            $table->unsignedBigInteger('platform_id')->comment('Id платформы');
            $table->unsignedBigInteger('file_id')->comment('Id загруженного файла');
            $table->unsignedBigInteger('release_type_id')->comment('Стадия релиза');
            $table->unsignedBigInteger('technical_requirement_id')->comment('Технические требования');
            $table->unsignedBigInteger('change_id')->comment('Id изменения');
            $table->text('description')->comment('Дополнительное описание');
            $table->boolean('is_ready')->default(false)->comment('Готов ли релиз к отображение на сайте для скачиванию');
            $table->boolean('is_public')->default(false)->comment('Опубликован ли релиз');
            $table->string('download_url')->comment('URL для скачивания');
            $table->string('version')->comment('Версия');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('file_id')->references('id')->on('files')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('platform_id')->references('id')->on('platforms')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('change_id')->references('id')->on('changes')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('release_type_id')->references('id')->on('releases_types')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('technical_requirement_id')->references('id')->on('technicals_requirements')->onDelete('cascade')->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('releases');
    }
};
