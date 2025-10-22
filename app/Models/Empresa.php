<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $table = 'empresas';
    
    protected $fillable = [
        'usuario_id',
        'nome',
        'razao_social',
        'nome_fantasia', 
        'cnpj',
        'inscricao_estadual',
        'regime_tributario',
        'situacao_cadastral',
        'capital_social',
        'porte',
        'natureza_juridica',
        'cnae_principal',
        'cnae_descricao',
        'cnae_fiscal',
        'cnae_fiscal_descricao',
        'cnaes_secundarios',
        'email',
        'telefone',
        'endereco',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'municipio',
        'uf',
        'cep',
        'data_inicio_atividade',
        'data_situacao_cadastral',
        'dados_brasilapi_atualizados_em',
        'pasta_backup',
        'ativo'
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'capital_social' => 'decimal:2',
        'cnaes_secundarios' => 'array',
        'data_inicio_atividade' => 'date',
        'data_situacao_cadastral' => 'date',
        'dados_brasilapi_atualizados_em' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relacionamento com usuário
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // Relacionamento com módulos (muitos para muitos)
    public function modulos()
    {
        return $this->belongsToMany(Modulo::class, 'empresa_modulos', 'empresa_id', 'modulo_id')
                    ->withPivot('ativo', 'data_ativacao', 'data_expiracao')
                    ->withTimestamps();
    }

    // Relacionamento com módulos ativos
    public function modulosAtivos()
    {
        return $this->modulos()->wherePivot('ativo', true);
    }

    // Relacionamento com pagamentos
    public function pagamentos()
    {
        return $this->hasMany(Pagamento::class, 'empresa_id');
    }

    // ========== RELACIONAMENTOS GESTÃO DE ENTREGAS ==========

    // Relacionamento com layouts de documentos
    public function layoutsDocumentos()
    {
        return $this->hasMany(LayoutDocumento::class, 'empresa_id');
    }

    // Relacionamento com layouts ativos
    public function layoutsDocumentosAtivos()
    {
        return $this->layoutsDocumentos()->where('ativo', true);
    }

    // Relacionamento com entregas
    public function entregas()
    {
        return $this->hasMany(Entrega::class, 'empresa_id');
    }

    // Relacionamento com contatos WhatsApp
    public function contatosWhatsApp()
    {
        return $this->hasMany(ContatoWhatsApp::class, 'empresa_id');
    }

    // Relacionamento com contatos ativos
    public function contatosWhatsAppAtivos()
    {
        return $this->contatosWhatsApp()->where('ativo', true);
    }

    // Relacionamento com contatos que recebem notificações
    public function contatosNotificacoes()
    {
        return $this->contatosWhatsApp()
                    ->where('ativo', true)
                    ->where('recebe_notificacoes', true);
    }

    // Relacionamento com sócios (QSA)
    public function socios()
    {
        return $this->hasMany(Socio::class, 'empresa_id');
    }

    // Relacionamento com obrigações fiscais (vínculos)
    public function obrigacoes()
    {
        return $this->hasMany(EmpresaObrigacao::class, 'empresa_id');
    }

    // Relacionamento com obrigações ativas
    public function obrigacoesAtivas()
    {
        return $this->obrigacoes()->where('ativo', true);
    }

    // Relacionamento com períodos fiscais através das obrigações
    public function periodosFiscais()
    {
        return $this->hasManyThrough(
            PeriodoFiscal::class,
            EmpresaObrigacao::class,
            'empresa_id',
            'empresa_obrigacao_id'
        );
    }

    // Scope para empresas ativas
    public function scopeAtivas($query)
    {
        return $query->where('ativo', true);
    }

    // Scope para empresas do usuário logado
    public function scopeDoUsuario($query, $usuarioId = null)
    {
        $usuarioId = $usuarioId ?? auth()->id();
        return $query->where('usuario_id', $usuarioId);
    }

    // Accessor para CNPJ formatado
    public function getCnpjFormatadoAttribute()
    {
        $cnpj = preg_replace('/\D/', '', $this->cnpj);
        return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $cnpj);
    }

    // Accessor para telefone formatado
    public function getTelefoneFormatadoAttribute()
    {
        $telefone = preg_replace('/\D/', '', $this->telefone);
        if (strlen($telefone) == 11) {
            return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $telefone);
        } elseif (strlen($telefone) == 10) {
            return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $telefone);
        }
        return $this->telefone;
    }

    // Accessor para capital social formatado
    public function getCapitalSocialFormatadoAttribute()
    {
        return $this->capital_social ? 'R$ ' . number_format($this->capital_social, 2, ',', '.') : 'R$ 0,00';
    }

    // Accessor para status da situação
    public function getSituacaoStatusAttribute()
    {
        if (empty($this->situacao_cadastral)) return 'unknown';
        
        $situacao = strtolower($this->situacao_cadastral);
        if (strpos($situacao, 'ativa') !== false) return 'active';
        if (strpos($situacao, 'suspensa') !== false) return 'suspended';
        if (strpos($situacao, 'baixada') !== false) return 'inactive';
        return 'unknown';
    }

    // Verifica se a empresa tem acesso a um módulo específico
    public function temAcessoAoModulo($moduloSlug)
    {
        return $this->modulosAtivos()
                    ->where('slug', $moduloSlug)
                    ->exists();
    }

    // Ativar um módulo para esta empresa
    public function ativarModulo($moduloId, $dataExpiracao = null)
    {
        return $this->modulos()->attach($moduloId, [
            'ativo' => true,
            'data_ativacao' => now(),
            'data_expiracao' => $dataExpiracao
        ]);
    }

    // Desativar um módulo para esta empresa
    public function desativarModulo($moduloId)
    {
        return $this->modulos()->updateExistingPivot($moduloId, [
            'ativo' => false
        ]);
    }

    // ========== MÉTODOS ÚTEIS - GESTÃO DE ENTREGAS ==========

    // Buscar entrega por competência (cria se não existir)
    public function obterEntrega($competencia)
    {
        return $this->entregas()->firstOrCreate(
            ['competencia' => $competencia],
            ['status' => 'aguardando']
        );
    }

    // Contar documentos pendentes total
    public function getTotalDocumentosPendentesAttribute()
    {
        return $this->entregas()
                    ->where('status', '!=', 'enviado')
                    ->sum('total_documentos');
    }

    // Contar documentos recebidos total
    public function getTotalDocumentosRecebidosAttribute()
    {
        return $this->entregas()
                    ->sum('documentos_recebidos');
    }

    // Contar entregas completas não enviadas
    public function getEntregasCompletasNaoEnviadasAttribute()
    {
        return $this->entregas()
                    ->where('status', 'completo')
                    ->count();
    }

    // ========== ACCESSORS ADICIONAIS ==========

    // Accessor para inscrição estadual formatada
    public function getInscricaoEstadualFormatadaAttribute()
    {
        return $this->inscricao_estadual ?? 'N/A';
    }

    // Accessor para regime tributário formatado
    public function getRegimeTributarioFormatadoAttribute()
    {
        $regimes = [
            'simples_nacional' => 'Simples Nacional',
            'lucro_presumido' => 'Lucro Presumido',
            'lucro_real' => 'Lucro Real',
            'mei' => 'MEI'
        ];

        return $regimes[$this->regime_tributario] ?? 'Não informado';
    }

    // Accessor para badge do regime tributário
    public function getRegimeTributarioBadgeAttribute()
    {
        $badges = [
            'simples_nacional' => '<span class="badge bg-info">Simples Nacional</span>',
            'lucro_presumido' => '<span class="badge bg-warning">Lucro Presumido</span>',
            'lucro_real' => '<span class="badge bg-danger">Lucro Real</span>',
            'mei' => '<span class="badge bg-success">MEI</span>'
        ];

        return $badges[$this->regime_tributario] ?? '<span class="badge bg-secondary">Não informado</span>';
    }

    // Accessor para porte formatado
    public function getPorteFormatadoAttribute()
    {
        $portes = [
            'MICRO' => 'Micro Empresa',
            'PEQUENO' => 'Pequeno Porte',
            'MEDIO' => 'Médio Porte',
            'GRANDE' => 'Grande Porte'
        ];

        return $portes[strtoupper($this->porte)] ?? $this->porte;
    }

    // Accessor para endereço completo
    public function getEnderecoCompletoAttribute()
    {
        $partes = array_filter([
            $this->logradouro,
            $this->numero ? "nº {$this->numero}" : null,
            $this->complemento,
            $this->bairro,
            $this->municipio ? "{$this->municipio}/{$this->uf}" : null,
            $this->cep ? "CEP: {$this->cep}" : null
        ]);

        return !empty($partes) ? implode(', ', $partes) : $this->endereco;
    }

    // Accessor para CEP formatado
    public function getCepFormatadoAttribute()
    {
        if (!$this->cep) return null;
        $cep = preg_replace('/\D/', '', $this->cep);
        return preg_replace('/(\d{5})(\d{3})/', '$1-$2', $cep);
    }

    // Verifica se dados da BrasilAPI estão desatualizados (>30 dias)
    public function dadosBrasilApiDesatualizados()
    {
        if (!$this->dados_brasilapi_atualizados_em) return true;
        
        return $this->dados_brasilapi_atualizados_em->lt(now()->subDays(30));
    }

    // Verifica se tem pasta de backup customizada
    public function temPastaBackupCustomizada()
    {
        return !empty($this->pasta_backup);
    }

    // Obter pasta de backup (customizada ou padrão)
    public function getPastaBackup()
    {
        if ($this->pasta_backup) {
            return $this->pasta_backup;
        }

        // Pasta padrão: storage/impostos_backup/{CNPJ}
        $cnpjLimpo = preg_replace('/\D/', '', $this->cnpj);
        return storage_path("app/impostos_backup/{$cnpjLimpo}");
    }
}

