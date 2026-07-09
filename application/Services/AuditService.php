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

    public function withChanges(array $before, array $after, array $extra = []): array
    {
        $changes = [];

        foreach ($after as $field => $newValue) {
            $oldValue = $before[$field] ?? null;
            if ($this->sameValue($oldValue, $newValue)) {
                continue;
            }

            $changes[$field] = [
                'antes' => $oldValue,
                'depois' => $newValue,
            ];
        }

        return array_merge($after, $extra, [
            'antes' => $before,
            'depois' => $after,
            'mudancas' => $changes,
        ]);
    }

    private function sameValue(mixed $oldValue, mixed $newValue): bool
    {
        if (is_numeric($oldValue) && is_numeric($newValue)) {
            return abs((float)$oldValue - (float)$newValue) < 0.0001;
        }

        if (is_array($oldValue) || is_array($newValue)) {
            return json_encode($oldValue, JSON_UNESCAPED_UNICODE) === json_encode($newValue, JSON_UNESCAPED_UNICODE);
        }

        return trim((string)($oldValue ?? '')) === trim((string)($newValue ?? ''));
    }
}
