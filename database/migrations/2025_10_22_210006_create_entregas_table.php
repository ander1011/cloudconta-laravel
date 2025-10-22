<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entregas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->string('competencia');
            $table->string('status')->default('aguardando');
            $table->integer('total_documentos')->default(0);
            $table->integer('documentos_recebidos')->default(0);
            $table->date('data_limite')->nullable();
            $table->timestamp('data_envio')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entregas');
    }
};
