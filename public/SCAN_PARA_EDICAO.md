# Escaneamento rápido para edição

Este documento resume **onde editar** no projeto `IndusOS Food` para mudanças comuns e traz um roteiro curto de varredura antes de começar.

## 0) Escaneamento inicial (2 minutos)

Use estes comandos para localizar rápido o que você precisa editar:

```bash
# Ver estrutura principal
rg --files

# Encontrar uma rota/método
rg "function .*Action|/clientes|/produtos" application

# Encontrar regra de negócio por palavra-chave
rg "estoque|pedido|receita|producao" application/Services application/Repositories

# Encontrar trecho visual de uma tela
rg "table|form|btn|modal" application/views
```

> Dica: ao achar o arquivo de entrada (controller), percorra o fluxo para `Service` e depois `Repository`.

## 1) Pontos de entrada

- `public/index.php`: front controller e definição principal das rotas.
- `application/bootstrap.php`: bootstrap da aplicação, autoload e inicialização.

## 2) Onde editar por tipo de mudança

### Regras de negócio
- Pasta: `application/Services/`
- Exemplos:
  - `PedidosService.php` (fluxo de pedidos)
  - `EstoqueService.php` (movimentações/validade)
  - `ProducaoService.php` (ordens de produção)

### Acesso ao banco
- Pasta: `application/Repositories/`
- Use quando precisar ajustar queries, joins, filtros e persistência.

### Endpoints e fluxo HTTP
- Painel (módulos): `application/Modules/*/Controllers/`
- API: `application/Controllers/Api/`
- CLI: `application/Controllers/Cli/`

### Telas e HTML
- Pasta: `application/views/`
- Subpastas por domínio (`clientes`, `produtos`, `producao`, `portal`, etc.).

### Configuração
- `application/config/app.php`
- `application/config/database.php`
- `.env` (credenciais, JWT, e base path)

### Banco de dados
- Estrutura inicial: `sql/schema.sql`
- Evoluções: `sql/migrations/`

## 3) Fluxo recomendado para editar com segurança

1. Ajustar Controller (entrada).
2. Ajustar Service (regra).
3. Ajustar Repository (dados), se necessário.
4. Ajustar View (saída), se houver impacto visual.
5. Validar rota manualmente.

## 3.1) Mapa rápido por cenário

- **Erro de cálculo/validação**: comece em `application/Services/`.
- **Dados incorretos em listagem**: confira `application/Repositories/` e depois a `View`.
- **Botão não funciona**: revise rota no controller e o HTML/JS da view correspondente.
- **Endpoint API falhando**: revisar `application/Controllers/Api/` + service envolvido.

## 4) Rotas-chave para validação manual

- Login: `/login`
- Portal: `/portal`
- Clientes: `/clientes`
- Produtos: `/produtos`
- Produção: `/producao/op`
- Estoque: `/estoque/posicao`
- Expedição: `/expedicao/picking/{pedido}`
- Financeiro: `/financeiro/titulos`

## 5) Checklist rápido antes de finalizar edição

- Conferir se não quebrou dependências entre `Controller -> Service -> Repository`.
- Verificar se a rota continua respondendo conforme esperado.
- Confirmar compatibilidade com `.env` (especialmente `APP_BASE_PATH`).
- Se alterar SQL, registrar também em `sql/migrations/`.
