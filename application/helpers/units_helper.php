<?php

declare(strict_types=1);

if (!function_exists('convertQty')) {
    /**
     * Converte quantidade entre unidades suportadas (KG<->G, L<->ML, UN sem conversão).
     */
    function convertQty(float $qty, string $fromUnit, string $toUnit): float
    {
        $from = strtoupper(trim($fromUnit));
        $to = strtoupper(trim($toUnit));

        if ($from === '' || $to === '') {
            throw new InvalidArgumentException('Unidade inválida para conversão.');
        }

        if ($from === $to) {
            return $qty;
        }

        $groups = [
            'weight' => ['KG' => 1000.0, 'G' => 1.0],
            'volume' => ['L' => 1000.0, 'ML' => 1.0],
            'unit' => ['UN' => 1.0],
        ];

        $fromGroup = null;
        $toGroup = null;
        foreach ($groups as $group => $units) {
            if (isset($units[$from])) {
                $fromGroup = $group;
            }
            if (isset($units[$to])) {
                $toGroup = $group;
            }
        }

        if ($fromGroup === null || $toGroup === null || $fromGroup !== $toGroup) {
            throw new InvalidArgumentException(sprintf('Não é possível converter %s para %s.', $from, $to));
        }

        $baseFactor = $groups[$fromGroup];
        $baseQty = $qty * $baseFactor[$from];
        return $baseQty / $baseFactor[$to];
    }
}
