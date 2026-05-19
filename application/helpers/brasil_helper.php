<?php
function somente_numeros(string $valor): string
{
    return preg_replace('/\D+/', '', $valor);
}

function valida_cpf(string $cpf): bool
{
    $cpf = somente_numeros($cpf);
    if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
        return false;
    }
    for ($t = 9; $t < 11; $t++) {
        $d = 0;
        for ($c = 0; $c < $t; $c++) {
            $d += $cpf[$c] * (($t + 1) - $c);
        }
        $d = ((10 * $d) % 11) % 10;
        if ($cpf[$c] != $d) {
            return false;
        }
    }
    return true;
}

function valida_cnpj(string $cnpj): bool
{
    $cnpj = somente_numeros($cnpj);
    if (strlen($cnpj) !== 14 || preg_match('/^(\d)\1{13}$/', $cnpj)) {
        return false;
    }
    $peso1 = [5,4,3,2,9,8,7,6,5,4,3,2];
    $peso2 = [6,5,4,3,2,9,8,7,6,5,4,3,2];
    for ($t = 0; $t < 2; $t++) {
        $peso = $t === 0 ? $peso1 : $peso2;
        $soma = 0;
        for ($i = 0; $i < count($peso); $i++) {
            $soma += intval($cnpj[$i]) * $peso[$i];
        }
        $resto = $soma % 11;
        $digito = $resto < 2 ? 0 : 11 - $resto;
        if (intval($cnpj[count($peso)]) !== $digito) {
            return false;
        }
        array_unshift($peso1, 6);
    }
    return true;
}

function formata_documento(string $valor): string
{
    $valor = somente_numeros($valor);
    if (strlen($valor) === 11) {
        return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $valor);
    }
    if (strlen($valor) === 14) {
        return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $valor);
    }
    return $valor;
}
