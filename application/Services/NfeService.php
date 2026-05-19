<?php
namespace App\Services;

use App\Services\ConfigService;

/**
 * Serviço simplificado para emissão de NF-e via API externa.
 * O objetivo é disponibilizar um ponto único para construir
 * o payload fiscal e enviá-lo para o provedor configurado.
 */
class NfeService
{
    private ConfigService $config;

    public function __construct(ConfigService $config)
    {
        $this->config = $config;
    }

    /**
     * Monta o payload básico para envio da NF-e.
     */
    public function montarPayload(array $pedido, array $cliente, array $itens, array $totais = []): array
    {
        return [
            'pedido' => [
                'id' => $pedido['id'] ?? null,
                'codigo' => $pedido['codigo'] ?? null,
                'data' => $pedido['created_at'] ?? date('Y-m-d H:i:s'),
                'valor' => $totais['valor'] ?? $pedido['total'] ?? 0,
                'descontos' => $totais['descontos'] ?? 0,
                'frete' => $totais['frete'] ?? 0,
                'observacoes' => $pedido['observacoes'] ?? null,
            ],
            'cliente' => [
                'nome' => $cliente['nome_fantasia'] ?? $cliente['razao_social'] ?? $cliente['nome'] ?? '',
                'documento' => $cliente['cnpj'] ?? $cliente['cpf'] ?? '',
                'email' => $cliente['email'] ?? '',
                'telefone' => $cliente['telefone'] ?? '',
                'endereco' => $cliente['endereco'] ?? [],
                'ie' => $cliente['ie'] ?? null,
                'im' => $cliente['im'] ?? null,
            ],
            'itens' => array_map(function (array $item) {
                return [
                    'sku' => $item['sku'] ?? null,
                    'descricao' => $item['nome'] ?? $item['descricao'] ?? '',
                    'quantidade' => (float)($item['quantidade'] ?? 0),
                    'unidade' => $item['unidade'] ?? 'un',
                    'valor_unitario' => (float)($item['preco_unit'] ?? $item['preco'] ?? 0),
                    'ncm' => $item['ncm'] ?? null,
                    'cest' => $item['cest'] ?? null,
                ];
            }, $itens),
        ];
    }

    /**
     * Envia a NF-e para a API configurada.
     */
    public function emitir(array $payload): array
    {
        $endpoint = rtrim((string)$this->config->get('NFE_API_URL', ''), '/');
        if ($endpoint === '') {
            throw new \RuntimeException('NFE_API_URL não configurada.');
        }

        $token = (string)$this->config->get('NFE_API_TOKEN', '');
        $body = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $ch = curl_init($endpoint.'/nfe');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer '.$token,
            ],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false) {
            throw new \RuntimeException('Falha ao comunicar com a API de NF-e: '.$error);
        }

        $decoded = json_decode($response, true);
        if ($status >= 400) {
            $mensagem = $decoded['message'] ?? $decoded['erro'] ?? 'Erro ao emitir NF-e';
            throw new \RuntimeException($mensagem);
        }

        return $decoded ?? [];
    }
}
