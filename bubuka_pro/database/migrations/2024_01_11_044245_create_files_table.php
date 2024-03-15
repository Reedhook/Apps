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
        Schema::create('files', function (Blueprint $table) {
            $table->id()->comment('Id');
            $table->string('name')->comment('Название файла');
            $table->string('path')->comment('Путь к файлу');
            $table->string('extension')->comment('Расширение файла');
            $table->string('mime_type')->comment('Тип содержимого файла');
            $table->integer('size')->comment('Размер файла в байтах');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
