<?php
namespace App\Repositories;

class ConfiguracoesRepository extends BaseRepository
{
    protected string $table = 'configuracoes';

    public function get(string $chave, ?string $default = null): ?string
    {
        $stmt = $this->db->prepare("SELECT valor FROM {$this->table} WHERE chave = :chave ORDER BY updated_at DESC");
        $stmt->execute(['chave' => $chave]);
        $valor = $stmt->fetchColumn();
        return $valor === false ? $default : $valor;
    }
}
