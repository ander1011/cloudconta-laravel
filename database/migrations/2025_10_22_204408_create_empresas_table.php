<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->string('nome');
            $table->string('razao_social')->nullable();
            $table->string('nome_fantasia')->nullable();
            $table->string('cnpj', 18)->unique();
            $table->string('inscricao_estadual')->nullable();
            $table->string('regime_tributario')->nullable();
            $table->string('situacao_cadastral')->nullable();
            $table->decimal('capital_social', 15, 2)->nullable();
            $table->string('porte')->nullable();
            $table->string('natureza_juridica')->nullable();
            $table->string('cnae_principal')->nullable();
            $table->text('cnae_descricao')->nullable();
            $table->string('cnae_fiscal')->nullable();
            $table->text('cnae_fiscal_descricao')->nullable();
            $table->json('cnaes_secundarios')->nullable();
            $table->string('email')->nullable();
            $table->string('telefone')->nullable();
            $table->text('endereco')->nullable();
            $table->string('logradouro')->nullable();
            $table->string('numero')->nullable();
            $table->string('complemento')->nullable();
            $table->string('bairro')->nullable();
            $table->string('municipio')->nullable();
            $table->string('uf', 2)->nullable();
            $table->string('cep', 10)->nullable();
            $table->date('data_inicio_atividade')->nullable();
            $table->date('data_situacao_cadastral')->nullable();
            $table->timestamp('dados_brasilapi_atualizados_em')->nullable();
            $table->string('pasta_backup')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};
