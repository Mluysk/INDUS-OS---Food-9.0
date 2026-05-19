<?php
declare(strict_types=1);

if (!function_exists('pix_crc16')) {
    function pix_crc16(string $payload): string
    {
        $polynomial = 0x1021;
        $result = 0xFFFF;

        $length = strlen($payload);
        for ($offset = 0; $offset < $length; $offset++) {
            $result ^= (ord($payload[$offset]) << 8);
            for ($bit = 0; $bit < 8; $bit++) {
                if (($result & 0x8000) !== 0) {
                    $result = ($result << 1) ^ $polynomial;
                } else {
                    $result <<= 1;
                }
                $result &= 0xFFFF;
            }
        }

        return strtoupper(str_pad(dechex($result), 4, '0', STR_PAD_LEFT));
    }
}

if (!function_exists('pix_field')) {
    function pix_field(string $id, string $value): string
    {
        $length = str_pad((string)strlen($value), 2, '0', STR_PAD_LEFT);
        return $id . $length . $value;
    }
}

if (!function_exists('gerar_payload_pix')) {
    function gerar_payload_pix(
        string $chave,
        float $valor = 0.0,
        string $nome = 'INDUSOS FOOD',
        string $cidade = 'SAO PAULO',
        string $txid = '***',
        ?string $descricao = null
    ): string {
        $nome = strtoupper(substr(trim($nome), 0, 25));
        $cidade = strtoupper(substr(trim($cidade), 0, 15) ?: 'SAO PAULO');
        $txidLimpo = preg_replace('/[^A-Za-z0-9]/', '', $txid) ?: '***';
        $txidLimpo = substr($txidLimpo, 0, 25);

        $gui = pix_field('00', 'br.gov.bcb.pix')
            . pix_field('01', $chave);
        if ($descricao) {
            $gui .= pix_field('02', substr($descricao, 0, 25));
        }

        $merchantAccount = pix_field('26', $gui);

        $payload = pix_field('00', '01');
        $payload .= pix_field('01', '12');
        $payload .= $merchantAccount;
        $payload .= pix_field('52', '0000');
        $payload .= pix_field('53', '986');

        if ($valor > 0) {
            $valorFmt = number_format($valor, 2, '.', '');
            $payload .= pix_field('54', $valorFmt);
        }

        $payload .= pix_field('58', 'BR');
        $payload .= pix_field('59', $nome);
        $payload .= pix_field('60', $cidade);
        $payload .= pix_field('62', pix_field('05', $txidLimpo));

        $payloadSemCrc = $payload . '6304';
        $crc = pix_crc16($payloadSemCrc);

        return $payloadSemCrc . $crc;
    }
}

