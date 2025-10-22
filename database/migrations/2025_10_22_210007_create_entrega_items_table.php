<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entrega_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entrega_id')->constrained('entregas')->onDelete('cascade');
            $table->string('tipo_documento');
            $table->string('nome_arquivo')->nullable();
            $table->string('caminho_arquivo')->nullable();
            $table->boolean('recebido')->default(false);
            $table->timestamp('data_recebimento')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entrega_items');
    }
};
