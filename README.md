# IndusOS Food

IndusOS Food é um sistema ERP simplificado para indústrias alimentícias construído em PHP 8.2+ seguindo uma arquitetura inspirada no CodeIgniter 3. Ele cobre módulos de clientes, produtos, produção, estoque, expedição, financeiro, relatórios e configuração, além de fornecer portal do cliente com autenticação JWT.

## Requisitos

* PHP >= 8.2 com extensões `pdo_mysql`, `openssl`, `mbstring`, `json`.
* MySQL 8 (charset `utf8mb4`).
* Extensão `zip` do PHP (opcional, mas recomendada para gerar XLSX mais compactos).

## Instalação

> **Sem Composer:** o projeto já inclui um autoloader próprio e bibliotecas internas para JWT, PDF e XLSX, portanto basta clonar o repositório e configurar o PHP; não é necessário executar `composer install`.

1. Clone o repositório e copie `.env.example` para `.env`, ajustando as credenciais de banco, SMTP e JWT.
   *Se instalar em uma subpasta (ex.: `https://dominio.com/erp`), defina `APP_BASE_PATH=/erp` no `.env` para que links e assets
   sejam gerados com o prefixo correto.*
2. Crie o banco de dados e execute o script `sql/schema.sql`.
3. Configure o servidor web apontando o DocumentRoot para `public/`.
   *Se isso não for possível (ex.: hospedagem compartilhada)*, mantenha o
   DocumentRoot na raiz do projeto e habilite `mod_rewrite`; o arquivo
   `.htaccess` incluído redirecionará automaticamente todas as requisições
   para `public/index.php`.
   > Em ambientes Apache, certifique-se de que o diretório onde o projeto foi instalado possui `AllowOverride All` habilitado;
   > caso contrário, URLs como `/login` retornarão **404 Not Found** em vez de chegarem ao front controller.
4. Ao rodar em ambientes locais como **XAMPP** (pastas em `htdocs/`), basta acessar a URL do projeto normalmente (`http://localhost/pasta/`).
   O `.htaccess` da raiz já encaminha as rotas para `public/index.php` e o front controller detecta automaticamente o subdiretório
   para atualizar menus, formulários e chamadas da API.

### Hospedagem na Hostinger (compatibilidade)

O projeto foi estruturado para funcionar em hospedagem compartilhada da Hostinger com PHP 8.2/8.3 e MySQL 8. Siga este checklist:

1. Crie o banco MySQL no painel (charset/collation `utf8mb4_general_ci`) e atualize as variáveis `DB_HOST`, `DB_NAME`, `DB_USER`
   e `DB_PASS` no `.env`.
2. Em "Sites > Gerenciar > Configurações avançadas > PHP" selecione PHP 8.2 ou 8.3 e habilite as extensões `pdo_mysql`,
   `mbstring`, `json`, `openssl` e `zip`.
3. Mantenha o **DocumentRoot** apontando para `public/`; se não puder alterar, deixe a raiz como está e use o `.htaccess`
   fornecido (já inclui o fallback para `public/index.php`).
   *Caso o site fique em uma pasta, defina `APP_BASE_PATH=/sua_pasta` no `.env` para normalizar rotas e assets.*
4. Confirme que **mod_rewrite** está ativo e que a opção "AllowOverride" está liberada para o diretório do site (necessário para
   URLs amigáveis do painel e da API).
5. Suba o conteúdo do repositório (incluindo `application/`, `public/` e `sql/`). Depois de enviar, importe o `sql/schema.sql`
   pelo phpMyAdmin.
6. Agende o cron para backups/alertas conforme abaixo e teste `/login` e `/portal` para validar a configuração.

## Estrutura

```
application/
  Core/           # Container, Router e classes base
  Services/       # Regras de negócio (RBAC, estoque, produção, etc.)
  Repositories/   # Acesso a dados via PDO
  Modules/        # Controladores do painel separados por domínio
  Controllers/    # Controladores globais (API, dashboard, CLI)
  views/portal/   # Portal web para clientes (catálogo + carrinho)
public/index.php  # Front controller e definição de rotas
sql/schema.sql    # DDL e seeds iniciais
```

## Usuário padrão

Após rodar o script SQL inicial, um usuário `admin@indusos.local` é criado com papel `admin` e senha `Admin@123`. Atualize a senha imediatamente em produção (recomenda-se re-hash com Argon2id se disponível).

## Backup via CLI

O comando abaixo gera dumps de banco e compacta uploads (ideal para cron de 6/6h na Hostinger):

```bash
php public/index.php cli/backup:run
```

## Rotinas de Validade

Agende um cron diário para executar um endpoint que leia `EstoqueService::alertasValidade` e envie e-mails. Ajuste `VALIDADE_ALERTA_DIAS` nas configurações.

## API do Cliente

* `POST /api/auth/token` – login (e-mail/CPF/CNPJ) para clientes.
* `GET /api/produtos` – lista produtos (token Bearer).
* `POST /api/pedidos` – cria pedido com reserva FEFO.
* `GET /api/pedidos/{id}` – detalhes do pedido.

## Portal do Cliente

O portal público disponível em `/portal` permite:

1. Cadastro com validação de CPF/CNPJ (módulo 11) e criação automática de usuário/cliente.
2. Login com geração de token JWT persistido no `localStorage`.
3. Consulta ao catálogo, montagem de carrinho e envio de pedidos integrados à API.

## Painel Interno

Rotas iniciam em `/clientes`, `/produtos`, `/producao/op`, `/estoque/posicao`, `/expedicao/picking/{pedido}`, `/financeiro/titulos`, `/relatorios/*` e `/config`. Todas as rotas retornam JSON neste protótipo; adapte as views conforme necessário.

## Swagger

Um arquivo OpenAPI simplificado é fornecido em `public/swagger.json` (adicione a rota no servidor web).

## Observações

Esta implementação fornece uma base funcional: regras de FEFO, geração de OP, baixa de estoque, emissão de PDF/XLSX (sem dependências externas) e auditoria via banco. Complete as views HTML, autenticação de sessão e layout responsivo conforme necessário para produção.
