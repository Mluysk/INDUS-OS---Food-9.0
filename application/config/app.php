<?php
return [
    'name' => 'IndusOS Food',
    'env' => getenv('APP_ENV') ?: 'development',
    'debug' => filter_var(getenv('APP_DEBUG') ?: false, FILTER_VALIDATE_BOOLEAN),
    'timezone' => getenv('TIMEZONE') ?: 'America/Sao_Paulo',
    'jwt_secret' => getenv('JWT_SECRET') ?: 'change_me',
    'validade_alerta_dias' => (int)(getenv('VALIDADE_ALERTA_DIAS') ?: 15),
    'pedido_baixa_no_criar' => filter_var(getenv('PEDIDO_BAIXA_NO_CRIAR') ?: 'false', FILTER_VALIDATE_BOOLEAN),
];
