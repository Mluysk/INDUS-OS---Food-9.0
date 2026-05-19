<?php
namespace App\Services;

use App\Repositories\AuditLogsRepository;

class AuditService
{
    private AuditLogsRepository $auditLogs;

    public function __construct(AuditLogsRepository $auditLogs)
    {
        $this->auditLogs = $auditLogs;
    }

    public function log(?int $usuarioId, string $acao, string $entidade, ?int $entidadeId, array $detalhes = []): void
    {
        $this->auditLogs->create([
            'usuario_id' => $usuarioId,
            'acao' => $acao,
            'entidade' => $entidade,
            'entidade_id' => $entidadeId,
            'detalhes' => json_encode($detalhes, JSON_UNESCAPED_UNICODE),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'cli',
        ]);
    }
}
