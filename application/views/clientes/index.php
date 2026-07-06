<section class="panel">
    <h2>Novo cliente</h2>
    <p class="panel-description">Cadastre clientes com CPF ou CNPJ válidos para liberar o acesso ao portal.</p>
    <form method="post" action="<?= htmlspecialchars($url('clientes'), ENT_QUOTES, 'UTF-8'); ?>" class="form-grid cliente-form">
        <div class="doc-toggle">
            <span>Documento principal</span>
            <div class="toggle">
                <button type="button" id="btn-cnpj" class="active" data-doc-target="cnpj" aria-pressed="true">CNPJ</button>
                <button type="button" id="btn-cpf" data-doc-target="cpf" aria-pressed="false">CPF</button>
            </div>
            <input type="hidden" name="documento_tipo" value="cnpj">
        </div>
        <label>
            Nome / Razão social
            <input type="text" name="razao_social" required>
        </label>
        <label>
            Nome fantasia
            <input type="text" name="nome_fantasia" required>
        </label>
        <label>
            <span id="documento-label" class="field-label-with-icon">
                <span class="icon" data-doc-label-icon data-lucide="building"></span>
                <span data-doc-label-text>CNPJ</span>
            </span>
            <input
                type="text"
                id="documento"
                name="documento"
                required
                placeholder="00.000.000/0000-00"
                maxlength="18"
                class="form-control"
                inputmode="numeric"
                autocomplete="off"
            />
        </label>
        <label>
            <span class="field-label-with-icon"><span class="icon" data-lucide="mail"></span><span>E-mail</span></span>
            <input type="email" name="email" required>
        </label>
        <div class="contact-row">
            <label class="contact-input-group">
                <span class="field-label-with-icon"><span class="icon" data-lucide="phone"></span><span>Contato</span></span>
                <input type="text" name="telefone" placeholder="(00) 00000-0000" required>
            </label>
            <div class="contact-type-group">
                <span class="contact-type-label">Tipo do contato</span>
                <div class="contact-type-toggle" role="radiogroup" aria-label="Tipo do contato">
                    <label class="contact-type-option">
                        <input type="radio" name="contato_tipo" value="celular" checked required>
                        <span class="icon" data-lucide="smartphone"></span>
                        <span class="contact-type-choice">
                            <span class="contact-type-dot" aria-hidden="true"></span>
                            <span class="contact-type-text">Celular</span>
                        </span>
                    </label>
                    <label class="contact-type-option">
                        <input type="radio" name="contato_tipo" value="telefone">
                        <span class="icon" data-lucide="phone"></span>
                        <span class="contact-type-choice">
                            <span class="contact-type-dot" aria-hidden="true"></span>
                            <span class="contact-type-text">Telefone</span>
                        </span>
                    </label>
                </div>
            </div>
            <label class="whatsapp-input-group">
                <span class="field-label-with-icon"><span class="icon" data-lucide="message-circle"></span><span>WhatsApp</span></span>
                <input type="text" name="whatsapp" placeholder="(00) 00000-0000" required>
            </label>
        </div>
        <div class="address-block">
            <div class="address-grid">
                <label>
                    CEP
                    <input type="text" name="cep" placeholder="00000-000" maxlength="9" inputmode="numeric" required>
                </label>
                <label>
                    Endereço
                    <input type="text" name="endereco" placeholder="Rua, avenida, etc." required>
                </label>
                <label>
                    Número
                    <input type="text" name="numero" placeholder="123" required>
                </label>
                <label>
                    Bairro
                    <input type="text" name="bairro" placeholder="Centro" required>
                </label>
                <label>
                    Cidade
                    <input type="text" name="cidade" placeholder="São Paulo" required>
                </label>
                <label>
                    Estado (UF)
                    <div class="uf-field">
                        <input type="text" name="estado" placeholder="SP" maxlength="2" required>
                        <img class="uf-flag" data-uf-flag alt="Bandeira da UF" hidden>
                    </div>
                </label>
            </div>
            <p class="cep-feedback" data-cep-status></p>
        </div>
        <div class="form-actions" style="grid-column: 1 / -1;">
            <button class="button button-primary" type="submit">Salvar cliente</button>
            <button class="button button-secondary" type="button" data-edit-cancel hidden>Cancelar edição</button>
        </div>
    </form>
</section>

<section class="panel">
    <div class="panel-header clientes-list-header">
        <div>
            <h2>Clientes cadastrados</h2>
            <p class="panel-description">Lista dos clientes com dados de contato e documentos.</p>
        </div>
        <div class="panel-actions clientes-list-actions">
            <?php $totalClientesCadastrados = count($clientes ?? []); ?>
            <span class="clientes-total-count" data-clientes-total-count>
                <span class="icon" data-lucide="users" aria-hidden="true"></span>
                <strong><?= $totalClientesCadastrados; ?></strong>
                <?= $totalClientesCadastrados === 1 ? 'cliente cadastrado' : 'clientes cadastrados'; ?>
            </span>
            <a
                class="print-link"
                data-tooltip="Imprimir relatorio completo"
                title="Imprimir relatorio completo"
                aria-label="Imprimir relatorio completo"
                target="_blank"
                rel="noreferrer"
                href="<?= htmlspecialchars($url('clientes/imprimir'), ENT_QUOTES, 'UTF-8'); ?>"
            >
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M6 9V3h12v6" />
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2" />
                    <path d="M6 14h12v7H6z" />
                </svg>
                Imprimir relatório
            </a>
        </div>
    </div>
    <?php
        $clienteCidades = [];
        $clienteEstados = [];
        $normalizarCidadeFiltro = static function (string $cidade): string {
            $cidade = trim($cidade);
            $cidade = strtr($cidade, [
                'Á' => 'A', 'À' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A',
                'á' => 'a', 'à' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a',
                'É' => 'E', 'È' => 'E', 'Ê' => 'E', 'Ë' => 'E',
                'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
                'Í' => 'I', 'Ì' => 'I', 'Î' => 'I', 'Ï' => 'I',
                'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ï' => 'i',
                'Ó' => 'O', 'Ò' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O',
                'ó' => 'o', 'ò' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o',
                'Ú' => 'U', 'Ù' => 'U', 'Û' => 'U', 'Ü' => 'U',
                'ú' => 'u', 'ù' => 'u', 'û' => 'u', 'ü' => 'u',
                'Ç' => 'C', 'ç' => 'c',
            ]);
            $cidade = strtolower($cidade);
            $cidade = preg_replace('/[^a-z0-9]+/', ' ', $cidade) ?? $cidade;
            return trim($cidade);
        };
        foreach (($clientes ?? []) as $clienteCidadeItem) {
            $cidadeOption = trim((string)($clienteCidadeItem['cidade'] ?? ''));
            $estadoOption = strtoupper(trim((string)($clienteCidadeItem['estado'] ?? '')));
            if ($estadoOption !== '') {
                $clienteEstados[$estadoOption] = $estadoOption;
            }
            if ($cidadeOption !== '') {
                $cidadeKey = $normalizarCidadeFiltro($cidadeOption);
                if ($cidadeKey === '') {
                    continue;
                }
                $cidadeAtual = $clienteCidades[$cidadeKey] ?? '';
                if ($cidadeAtual === '' || ($cidadeAtual === strtoupper($cidadeAtual) && $cidadeOption !== strtoupper($cidadeOption))) {
                    $clienteCidades[$cidadeKey] = $cidadeOption;
                }
            }
        }
        natcasesort($clienteCidades);
        ksort($clienteEstados);
    ?>
    <div class="clientes-filter-bar" aria-label="Filtros da lista de clientes">
        <label class="clientes-search-box">
            <span class="icon" data-lucide="search" aria-hidden="true"></span>
            <input type="search" data-clientes-search placeholder="Buscar por ID, nome, CPF/CNPJ, contato ou cidade" aria-label="Buscar clientes">
        </label>
        <div class="clientes-filter-field clientes-filter-field--city">
            <label for="clientes-city-filter">Cidade</label>
            <div class="clientes-city-combobox" data-clientes-city-combobox>
                <input
                    id="clientes-city-filter"
                    type="text"
                    data-clientes-city-filter
                    placeholder="Todas as cidades"
                    autocomplete="off"
                    role="combobox"
                    aria-autocomplete="list"
                    aria-expanded="false"
                    aria-controls="clientes-city-options"
                >
                <button type="button" class="clientes-city-combobox__toggle" data-clientes-city-toggle aria-label="Abrir lista de cidades" title="Abrir lista de cidades">
                    <span class="icon" data-lucide="chevron-down" aria-hidden="true"></span>
                </button>
                <div class="clientes-city-combobox__options" id="clientes-city-options" data-clientes-city-options role="listbox" hidden>
                    <button type="button" class="clientes-city-combobox__option" data-clientes-city-option data-city-value="" role="option">Todas as cidades</button>
                    <?php foreach ($clienteCidades as $cidadeKey => $cidadeOption): ?>
                        <button
                            type="button"
                            class="clientes-city-combobox__option"
                            data-clientes-city-option
                            data-city-value="<?= htmlspecialchars($cidadeKey, ENT_QUOTES, 'UTF-8'); ?>"
                            role="option"
                        ><?= htmlspecialchars($cidadeOption, ENT_QUOTES, 'UTF-8'); ?></button>
                    <?php endforeach; ?>
                    <div class="clientes-city-combobox__empty" data-clientes-city-empty hidden>Nenhuma cidade encontrada</div>
                </div>
            </div>
        </div>
        <label class="clientes-filter-field clientes-filter-field--uf">
            <span>UF</span>
            <select data-clientes-uf-filter>
                <option value="">Todas</option>
                <?php foreach ($clienteEstados as $estadoOption): ?>
                    <option value="<?= htmlspecialchars($estadoOption, ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($estadoOption, ENT_QUOTES, 'UTF-8'); ?></option>
                <?php endforeach; ?>
            </select>
        </label>
    </div>
    <div style="overflow-x: auto;">
        <table class="data-table clientes-table">
            <thead>
                <tr>
                    <th><button type="button" class="clientes-sort-head" data-clientes-sort-field="id" data-clientes-sort-type="number">ID <span>⇅</span></button></th>
                    <th><button type="button" class="clientes-sort-head" data-clientes-sort-field="nome" data-clientes-sort-type="text">Nome <span>⇅</span></button></th>
                    <th><button type="button" class="clientes-sort-head" data-clientes-sort-field="cnpj" data-clientes-sort-type="text">CNPJ <span>⇅</span></button></th>
                    <th><button type="button" class="clientes-sort-head" data-clientes-sort-field="cpf" data-clientes-sort-type="text">CPF <span>⇅</span></button></th>
                    <th><button type="button" class="clientes-sort-head" data-clientes-sort-field="contato" data-clientes-sort-type="text">Contato <span>⇅</span></button></th>
                    <th><button type="button" class="clientes-sort-head" data-clientes-sort-field="cidade" data-clientes-sort-type="text">Cidade/UF <span>⇅</span></button></th>
                    <th></th>
                </tr>
            </thead>
            <tbody data-clientes-tbody>
                <?php if ($clientes): ?>
                    <?php foreach ($clientes as $cliente): ?>
                        <?php
                            $clienteNomeBusca = trim((string)(($cliente['razao_social'] ?? '').' '.($cliente['nome_fantasia'] ?? '')));
                            $clienteDocBusca = trim((string)(($cliente['cnpj'] ?? '').' '.($cliente['cpf'] ?? '')));
                            $clienteContatoBusca = trim((string)(($cliente['telefone'] ?? '').' '.($cliente['whatsapp'] ?? '').' '.($cliente['email'] ?? '')));
                            $clienteCidadeBusca = trim((string)(($cliente['cidade'] ?? '').' '.($cliente['estado'] ?? '')));
                            $clienteIdBusca = (string)($cliente['id'] ?? '');
                            $clienteBuscaGeral = trim($clienteIdBusca.' '.$clienteNomeBusca.' '.$clienteDocBusca.' '.$clienteContatoBusca.' '.$clienteCidadeBusca);
                            $clienteCidadeNormalizada = $normalizarCidadeFiltro((string)($cliente['cidade'] ?? ''));
                        ?>
                        <tr
                            data-cliente-row
                            data-cliente-search="<?= htmlspecialchars($clienteBuscaGeral, ENT_QUOTES, 'UTF-8'); ?>"
                            data-cliente-name="<?= htmlspecialchars($clienteNomeBusca, ENT_QUOTES, 'UTF-8'); ?>"
                            data-cliente-city="<?= htmlspecialchars($clienteCidadeNormalizada, ENT_QUOTES, 'UTF-8'); ?>"
                            data-cliente-uf="<?= htmlspecialchars(strtoupper(trim((string)($cliente['estado'] ?? ''))), ENT_QUOTES, 'UTF-8'); ?>"
                            data-sort-id="<?= (int)$cliente['id']; ?>"
                            data-sort-nome="<?= htmlspecialchars($clienteNomeBusca, ENT_QUOTES, 'UTF-8'); ?>"
                            data-sort-cnpj="<?= htmlspecialchars((string)($cliente['cnpj'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                            data-sort-cpf="<?= htmlspecialchars((string)($cliente['cpf'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                            data-sort-contato="<?= htmlspecialchars($clienteContatoBusca, ENT_QUOTES, 'UTF-8'); ?>"
                            data-sort-cidade="<?= htmlspecialchars($clienteCidadeBusca, ENT_QUOTES, 'UTF-8'); ?>"
                        >
                            <td><?= (int)$cliente['id']; ?></td>
                            <td><?= htmlspecialchars($cliente['razao_social'] ?? $cliente['nome_fantasia'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?= htmlspecialchars($cliente['cnpj'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?= htmlspecialchars($cliente['cpf'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?= htmlspecialchars($cliente['telefone'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                            <?php
                                $cidade = trim((string)($cliente['cidade'] ?? ''));
                                $estado = strtoupper(trim((string)($cliente['estado'] ?? '')));
                            ?>
                            <td>
                                <?php if ($cidade !== ''): ?><span class="cliente-city"><?= htmlspecialchars($cidade, ENT_QUOTES, 'UTF-8'); ?></span><?php endif; ?>
                                <?php if ($cidade !== '' && $estado !== ''): ?> / <?php endif; ?>
                                <?php if ($estado !== ''): ?><span class="cliente-uf"><?= htmlspecialchars($estado, ENT_QUOTES, 'UTF-8'); ?></span><?php endif; ?>
                                <?php if ($cidade === '' && $estado === ''): ?>-<?php endif; ?>
                            </td>
                            <?php
                                $clientePayload = [
                                    'id' => $cliente['id'] ?? null,
                                    'razao_social' => $cliente['razao_social'] ?? null,
                                    'nome_fantasia' => $cliente['nome_fantasia'] ?? null,
                                    'cnpj' => $cliente['cnpj'] ?? null,
                                    'cpf' => $cliente['cpf'] ?? null,
                                    'email' => $cliente['email'] ?? null,
                                    'telefone' => $cliente['telefone'] ?? null,
                                    'contato_tipo' => $cliente['contato_tipo'] ?? null,
                                    'whatsapp' => $cliente['whatsapp'] ?? null,
                                    'endereco' => $cliente['endereco'] ?? null,
                                    'numero' => $cliente['numero'] ?? null,
                                    'bairro' => $cliente['bairro'] ?? null,
                                    'cidade' => $cliente['cidade'] ?? null,
                                    'estado' => isset($cliente['estado']) ? strtoupper((string)$cliente['estado']) : null,
                                    'cep' => $cliente['cep'] ?? null,
                                    'created_at' => $cliente['created_at'] ?? null,
                                ];
                            ?>
                            <td class="table-actions">
                                <button type="button"
                                        class="table-action js-cliente-detalhe"
                                        data-endpoint="<?= htmlspecialchars($url('clientes/'.$cliente['id']), ENT_QUOTES, 'UTF-8'); ?>"
                                        data-cliente='<?= htmlspecialchars((string)json_encode($clientePayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8'); ?>'>
                                    <span class="icon" data-lucide="contact"></span>
                                    <span>Detalhes</span>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">Nenhum cliente cadastrado ainda.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="list-pagination" data-list-pagination="clientes" aria-label="Paginação da lista de clientes"></div>
</section>

<div class="cliente-modal" data-modal data-base-url="<?= htmlspecialchars($url('clientes'), ENT_QUOTES, 'UTF-8'); ?>" hidden>
    <div class="cliente-modal__backdrop" data-modal-close></div>
    <div class="cliente-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="cliente-modal-title">
        <header class="cliente-modal__header">
            <div>
                <h3 id="cliente-modal-title">Detalhes do cliente</h3>
                <p class="cliente-modal__subtitle" data-modal-subtitle></p>
            </div>
            <button class="cliente-modal__close" type="button" data-modal-close aria-label="Fechar">
                <span data-lucide="x"></span>
            </button>
        </header>
        <div class="cliente-modal__content">
            <dl>
                <div><dt>ID</dt><dd data-field="id"></dd></div>
                <div><dt>Razão social</dt><dd data-field="razao_social"></dd></div>
                <div><dt>Nome fantasia</dt><dd data-field="nome_fantasia"></dd></div>
                <div><dt>CNPJ</dt><dd data-field="cnpj"></dd></div>
                <div><dt>CPF</dt><dd data-field="cpf"></dd></div>
                <div><dt>E-mail</dt><dd data-field="email"></dd></div>
                <div><dt>Contato</dt><dd data-field="telefone"></dd></div>
                <div><dt>Tipo contato</dt><dd data-field="contato_tipo"></dd></div>
                <div><dt>WhatsApp</dt><dd data-field="whatsapp"></dd></div>
                <div><dt>Endereço</dt><dd data-field="endereco"></dd></div>
                <div><dt>Número</dt><dd data-field="numero"></dd></div>
                <div><dt>Bairro</dt><dd data-field="bairro"></dd></div>
                <div><dt>Cidade</dt><dd data-field="cidade"></dd></div>
                <div><dt>Estado</dt><dd data-field="estado"></dd></div>
                <div><dt>CEP</dt><dd data-field="cep"></dd></div>
                <div><dt>Criado em</dt><dd data-field="created_at"></dd></div>
            </dl>
        </div>
        <footer class="cliente-modal__footer">
            <div class="cliente-modal__actions">
                <button type="button" class="cliente-modal__action cliente-modal__action--edit" data-modal-edit>
                    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                        <path d="M4 16.75V20h3.25L17.81 9.44l-3.25-3.25L4 16.75z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                        <path d="M14.56 6.19l2.12-2.12a1.5 1.5 0 0 1 2.12 0l1.12 1.12a1.5 1.5 0 0 1 0 2.12l-2.12 2.12" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                    <span>Editar</span>
                </button>
                <button type="button" class="cliente-modal__action cliente-modal__action--print" data-modal-print>
                    <span data-lucide="printer"></span>
                    <span>Imprimir</span>
                </button>
                <button type="button" class="cliente-modal__action cliente-modal__action--delete" data-modal-delete>
                    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                        <path d="M4 7h16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        <path d="M9 7V5a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        <path d="M6 7l1 12a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2l1-12" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M10 11v6M14 11v6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                    <span>Excluir</span>
                </button>
            </div>
            <button type="button" class="button" data-modal-close>Fechar</button>
        </footer>
    </div>
</div>

<style>
    .cliente-form {
        position: relative;
        gap: 1.2rem;
    }

    .field-label-with-icon {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }

    .field-label-with-icon .icon {
        width: 0.95rem;
        height: 0.95rem;
        color: var(--text-muted);
    }

    .contact-row {
        grid-column: 1 / -1;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 1rem;
    }

    .contact-input-group {
        margin: 0;
        flex: 1 1 0;
        min-width: 240px;
        max-width: 380px;
    }

    .whatsapp-input-group {
        margin: 0 0 0 26px;
        flex: 1 1 0;
        min-width: 240px;
        max-width: 400px;
    }

    .contact-type-group {
        flex: 0 0 auto;
        width: auto;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-self: center;
    }

    .contact-type-label {
        display: block;
        margin-bottom: 0.4rem;
        font-size: 0.76rem;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: var(--text-muted);
        font-weight: 700;
    }

    .contact-type-toggle {
        display: inline-flex;
        align-items: flex-end;
        gap: 1.05rem;
    }

    .contact-type-option {
        position: relative;
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        gap: 0.12rem;
        cursor: pointer;
        font-size: 0.82rem;
        font-weight: 600;
        white-space: nowrap;
        padding: 0;
        border: 0;
        background: transparent;
    }

    .contact-type-option input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .contact-type-choice {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        line-height: 1;
    }

    .contact-type-dot {
        width: 0.72rem;
        height: 0.72rem;
        border-radius: 999px;
        border: 2px solid var(--border-strong, #64748b);
        display: inline-block;
        box-sizing: border-box;
    }

    .contact-type-option input:checked ~ .contact-type-choice .contact-type-dot {
        border-color: #ef4444;
        background: radial-gradient(circle at center, #ef4444 45%, transparent 48%);
    }

    .contact-type-text {
        display: inline-flex;
        align-items: center;
        margin: 0;
        color: var(--text-muted);
    }

    .contact-type-option input:checked ~ .contact-type-choice .contact-type-text {
        color: var(--text-strong);
    }

    .contact-type-option .icon {
        width: 0.95rem;
        height: 0.95rem;
        display: inline-flex !important;
        margin: 0 0 0.1rem !important;
        color: var(--text-muted);
    }

    .contact-type-option input:checked ~ .icon {
        color: var(--text-muted);
    }

    .address-block {
        grid-column: 1 / -1;
        background: var(--surface-muted);
        border-radius: 16px;
        padding: 1.1rem 1.2rem;
        border: 1px solid var(--border-subtle);
    }

    .address-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
    }

    .address-grid label input {
        margin-top: 0.35rem;
    }

    .uf-field {
        position: relative;
        display: flex;
        align-items: center;
        width: fit-content;
        min-width: 88px;
        max-width: 120px;
    }

    .uf-field input[name="estado"] {
        width: 100%;
        height: calc(2rem + 5px);
        line-height: 1.2;
        padding: 0.2rem 3.6rem 0.2rem 0.55rem;
        box-sizing: border-box;
        text-transform: uppercase;
    }

    .uf-flag {
        position: absolute;
        right: 0.5rem;
        top: calc(50% + 2px) !important;
        transform: translateY(-50%) !important;
        width: 36px;
        height: 26px;
        border-radius: 3px;
        border: 1px solid rgba(15, 23, 42, 0.10);
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.10);
        object-fit: cover;
        background: #fff;
        pointer-events: none;
    }

    .cep-feedback {
        margin-top: 0.75rem;
        font-size: 0.82rem;
        color: var(--text-muted);
    }

    .cep-feedback[data-state="error"] {
        color: var(--danger-strong, #dc2626);
    }

    .cep-feedback[data-state="success"] {
        color: var(--success-strong, #047857);
    }

    .doc-toggle {
        grid-column: 1 / -1;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        background: var(--surface-muted);
        border-radius: 14px;
        padding: 0.75rem 1rem;
        border: 1px solid var(--border-subtle);
    }

    .doc-toggle > span {
        font-weight: 600;
        color: var(--text-strong);
    }

    .doc-toggle .toggle {
        display: inline-flex;
        background: var(--surface-elevated);
        border-radius: 12px;
        padding: 0.25rem;
        gap: 0.25rem;
        border: 1px solid var(--border-subtle);
    }

    .doc-toggle .toggle button {
        appearance: none;
        border: none;
        background: transparent;
        padding: 0.35rem 0.85rem;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.82rem;
        letter-spacing: 0.01em;
        cursor: pointer;
        color: var(--text-muted);
        transition: all 0.2s ease;
    }

    .doc-toggle .toggle button.active {
        background: var(--primary);
        color: #fff;
        box-shadow: 0 6px 16px rgba(var(--brand-rgb), 0.22);
    }

    @media (max-width: 720px) {
        .doc-toggle {
            align-items: flex-start;
            gap: 0.75rem;
        }

        .doc-toggle .toggle {
            width: 100%;
            justify-content: space-between;
        }

        .doc-toggle .toggle button {
            flex: 1 1 auto;
            text-align: center;
        }

        .uf-field {
            width: fit-content;
            min-width: 88px;
            max-width: 120px;
        }

        .contact-row {
            flex-direction: column;
            align-items: stretch;
        }

        .contact-input-group,
        .whatsapp-input-group,
        .contact-type-group {
            flex: 1 1 100%;
            max-width: none;
            width: 100%;
        }

        .whatsapp-input-group {
            margin-left: 0;
        }
    }

    .panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .panel-actions {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .panel-actions .button {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
    }

    .clientes-list-header {
        align-items: flex-start;
    }

    .clientes-list-actions {
        flex-direction: column;
        align-items: center;
        gap: 12px;
        margin-left: auto;
    }

    .clientes-list-actions .print-link {
        align-self: center;
    }

    .clientes-total-count {
        display: inline-flex;
        align-items: center;
        gap: 0.38rem;
        min-height: 36px;
        transform: translateY(-9px);
        color: var(--text-muted, #64748b);
        font-size: 0.82rem;
        font-weight: 750;
        white-space: nowrap;
    }

    .clientes-total-count .icon {
        width: 17px;
        height: 17px;
        color: var(--primary, #4f46e5);
    }

    .clientes-total-count strong {
        color: var(--text, #111827);
        font-size: 0.9rem;
        font-weight: 900;
    }

    .print-link {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        padding: 0;
        border: 1px solid var(--border, #e5e7eb);
        border-radius: 10px;
        background: #fff;
        color: var(--text, #111827);
        font-size: 0;
        font-weight: 700;
        text-decoration: none;
        transition: border-color 160ms ease, color 160ms ease, box-shadow 160ms ease, transform 160ms ease;
    }

    .print-link:hover,
    .print-link:focus-visible {
        border-color: var(--primary, #4b5563);
        color: var(--primary, #4b5563);
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.10);
        transform: translateY(-1px);
        outline: 0;
    }

    .print-link svg {
        width: 19px;
        height: 19px;
    }

    .print-link::after {
        content: attr(data-tooltip);
        position: absolute;
        right: 0;
        bottom: calc(100% + 8px);
        z-index: 30;
        width: max-content;
        max-width: 220px;
        padding: 7px 9px;
        border-radius: 8px;
        background: #111827;
        color: #fff;
        font-size: 0.76rem;
        font-weight: 700;
        line-height: 1.2;
        pointer-events: none;
        opacity: 0;
        transform: translateY(4px);
        transition: opacity 140ms ease, transform 140ms ease;
    }

    .print-link:hover::after,
    .print-link:focus-visible::after {
        opacity: 1;
        transform: translateY(0);
    }

    .clientes-filter-bar {
        display: flex;
        align-items: flex-end;
        gap: 12px;
        flex-wrap: wrap;
        margin: 4px 0 16px;
    }

    .clientes-search-box {
        display: inline-flex;
        align-items: center;
        gap: 0.62rem;
        width: min(100%, calc(48ch + 72px));
        min-height: 42px;
        border: 1px solid var(--border, #e5e7eb);
        border-radius: 999px;
        padding: 0.55rem 1.05rem;
        background: var(--surface, #fff);
        color: var(--text-muted, #64748b);
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
    }

    .clientes-search-box .icon {
        width: 19px;
        height: 19px;
        flex: 0 0 19px;
        color: currentColor;
        stroke-width: 2.2;
    }

    .clientes-search-box input {
        width: 100%;
        min-width: 0;
        border: 0;
        padding: 0;
        background: transparent;
        color: var(--text, #111827);
        outline: 0;
        font-weight: 800;
        font-size: 0.78rem;
    }

    .clientes-search-box input::placeholder {
        opacity: 0.85;
    }

    .clientes-search-box input:focus {
        border: 0 !important;
        box-shadow: none !important;
    }

    .clientes-filter-field {
        display: grid;
        gap: 5px;
        min-width: 190px;
        color: var(--text-muted, #64748b);
        font-size: 0.78rem;
        font-weight: 800;
    }

    .clientes-filter-field input,
    .clientes-filter-field select {
        min-height: 42px;
        border: 1px solid var(--border, #e5e7eb);
        border-radius: 14px;
        padding: 0.55rem 0.85rem;
        background: var(--surface, #fff);
        color: var(--text, #111827);
        font-weight: 800;
        outline: 0;
    }

    .clientes-filter-field--city {
        min-width: 220px;
    }

    .clientes-city-combobox {
        position: relative;
        min-width: 220px;
    }

    .clientes-city-combobox > input {
        width: 100%;
        padding-right: 2.75rem;
        box-sizing: border-box;
        text-transform: uppercase;
    }

    .clientes-city-combobox__toggle {
        position: absolute;
        top: 0;
        right: 0;
        z-index: 2;
        width: 42px;
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 0;
        padding: 0;
        background: transparent;
        color: var(--text-muted, #64748b);
        cursor: pointer;
    }

    .clientes-city-combobox__toggle .icon {
        width: 17px;
        height: 17px;
        transition: transform 0.18s ease;
    }

    .clientes-city-combobox.is-open .clientes-city-combobox__toggle .icon {
        transform: rotate(180deg);
    }

    .clientes-city-combobox__options {
        position: absolute;
        top: calc(100% + 6px);
        left: 0;
        z-index: 40;
        width: max(100%, 260px);
        max-height: 240px;
        overflow-y: auto;
        border: 1px solid var(--border, #e5e7eb);
        border-radius: 8px;
        padding: 5px;
        background: var(--surface, #fff);
        box-shadow: 0 18px 40px -24px rgba(15, 23, 42, 0.42);
    }

    .clientes-city-combobox__options[hidden],
    .clientes-city-combobox__option[hidden],
    .clientes-city-combobox__empty[hidden] {
        display: none;
    }

    .clientes-city-combobox__option {
        width: 100%;
        min-height: 36px;
        display: flex;
        align-items: center;
        border: 0;
        border-radius: 6px;
        padding: 0.5rem 0.65rem;
        background: transparent;
        color: var(--text, #111827);
        font: inherit;
        font-weight: 700;
        text-align: left;
        cursor: pointer;
        text-transform: uppercase;
    }

    .clientes-city-combobox__option:hover,
    .clientes-city-combobox__option:focus-visible,
    .clientes-city-combobox__option[aria-selected="true"] {
        background: var(--surface-muted, #f1f5f9);
        color: var(--primary, #4f46e5);
        outline: 0;
    }

    .clientes-city-combobox__empty {
        padding: 0.7rem;
        color: var(--text-muted, #64748b);
        font-weight: 700;
    }

    .clientes-filter-field--uf {
        min-width: 110px;
    }

    .clientes-filter-field--uf select,
    .cliente-city,
    .cliente-uf,
    .cliente-modal__content [data-field="estado"] {
        text-transform: uppercase;
    }

    .clientes-filter-field input:focus,
    .clientes-filter-field select:focus,
    .clientes-city-combobox:focus-within > input,
    .clientes-search-box:focus-within {
        border-color: var(--primary, #4f46e5);
        box-shadow: var(--primary-glow, 0 0 0 3px rgba(79, 70, 229, 0.20)), 0 12px 26px -22px rgba(var(--primary-rgb, 79, 70, 229), 0.74);
    }

    .clientes-search-box:focus-within .icon {
        color: var(--primary, #4f46e5);
    }

    .clientes-table .table-actions {
        text-align: right;
        white-space: nowrap;
    }

    .clientes-sort-head {
        display: inline-flex;
        align-items: center;
        gap: 0.38rem;
        border: 0;
        padding: 0;
        color: inherit;
        background: transparent;
        font: inherit;
        font-weight: 800;
        text-transform: inherit;
        letter-spacing: inherit;
        cursor: pointer;
    }

    .clientes-sort-head > span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 21px;
        height: 21px;
        border-radius: 999px;
        background: var(--surface-muted, #eef2f7);
        color: var(--text-muted, #64748b);
        font-size: 0.78rem;
        line-height: 1;
    }

    .clientes-sort-head:hover > span,
    .clientes-sort-head > span.active {
        color: var(--primary, #4f46e5);
        background: color-mix(in srgb, var(--primary, #4f46e5) 12%, #fff);
    }

    .list-pagination {
        display: grid;
        grid-template-columns: minmax(120px, 1fr) auto minmax(220px, 1fr);
        align-items: center;
        gap: 12px;
        margin-top: 14px;
        color: var(--text-muted, #64748b);
        font-size: 0.85rem;
        font-weight: 800;
    }

    .list-pagination-pages,
    .list-pagination-size {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .list-pagination-pages { justify-content: center; }
    .list-pagination-size { justify-content: flex-end; }

    .list-page-button,
    .list-page-input {
        min-width: 38px;
        min-height: 38px;
        border: 1px solid var(--border, #e5e7eb);
        border-radius: 999px;
        background: var(--surface, #fff);
        color: var(--text, #111827);
        font-weight: 850;
        text-align: center;
    }

    .list-page-button {
        padding: 7px 12px;
        cursor: pointer;
    }

    .list-page-button.active {
        border-color: var(--primary, #4f46e5);
        background: var(--primary, #4f46e5);
        color: #fff;
    }

    .list-page-input {
        width: 58px;
        padding: 7px 10px;
    }

    .list-page-input[data-client-page-input] {
        width: calc(5ch + 32px);
    }

    .list-page-size-select {
        min-height: 38px;
        border: 1px solid var(--border, #e5e7eb);
        border-radius: 999px;
        padding: 7px 34px 7px 14px;
        background: var(--surface, #fff);
        color: var(--text, #111827);
        font-weight: 850;
    }

    @media (max-width: 760px) {
        .list-pagination { grid-template-columns: 1fr; }
        .list-pagination-pages,
        .list-pagination-size { justify-content: flex-start; }
    }

    .table-action {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.35rem 0.75rem;
        border-radius: 999px;
        border: 1px solid transparent;
        font-size: 0.8rem;
        font-weight: 600;
        background: var(--surface-elevated);
        color: var(--text-strong);
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .table-action:hover {
        border-color: rgba(var(--brand-rgb), 0.35);
        box-shadow: 0 6px 16px rgba(var(--brand-rgb), 0.18);
        color: var(--brand-strong);
    }

    .table-action .icon {
        width: 16px;
        height: 16px;
    }

    .table-action.is-loading {
        opacity: 0.6;
        pointer-events: none;
    }

    .cliente-modal[hidden] {
        display: none;
    }

    .cliente-modal {
        position: fixed;
        inset: 0;
        z-index: 1300;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
    }

    .cliente-modal__backdrop {
        position: absolute;
        inset: 0;
        background: rgba(15, 23, 42, 0.45);
        backdrop-filter: blur(4px);
    }

    .cliente-modal__dialog {
        position: relative;
        background: #ffffff;
        color: var(--text-strong);
        border-radius: 24px;
        padding: 1.6rem;
        width: min(640px, 100%);
        max-height: min(90vh, 720px);
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
        box-shadow: 0 24px 60px rgba(15, 23, 42, 0.25);
    }

    body.theme-dark .cliente-modal__dialog {
        background: #0f172a;
    }

    .cliente-modal__header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
    }

    .cliente-modal__header h3 {
        margin: 0;
        font-size: 1.25rem;
    }

    .cliente-modal__subtitle {
        margin: 0.2rem 0 0;
        font-size: 0.88rem;
        color: var(--text-muted);
    }

    .cliente-modal__close {
        appearance: none;
        border: none;
        background: var(--surface-muted);
        border-radius: 999px;
        padding: 0.35rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: var(--text-muted);
        transition: all 0.2s ease;
    }

    .cliente-modal__close:hover {
        color: var(--text-strong);
        transform: rotate(90deg);
    }

    .cliente-modal__content {
        overflow: auto;
        padding-right: 0.25rem;
    }

    .cliente-modal__content dl {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1rem 1.4rem;
        margin: 0;
    }

    .cliente-modal__content dt {
        font-size: 0.78rem;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: var(--text-muted);
        margin-bottom: 0.35rem;
    }

    .cliente-modal__content dd {
        margin: 0;
        font-size: 0.95rem;
        font-weight: 600;
        color: var(--text-strong);
        word-break: break-word;
    }

    .cliente-modal__footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .cliente-modal__actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.6rem;
    }

    .cliente-modal__action {
        border: 1px solid var(--border);
        background: var(--surface-alt);
        color: var(--text);
        border-radius: 999px;
        padding: 0.45rem 0.95rem;
        font-weight: 600;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .cliente-modal__action svg {
        width: 16px;
        height: 16px;
    }

    .cliente-modal__action--edit:hover {
        color: var(--primary-dark);
        border-color: rgba(var(--brand-rgb), 0.35);
        box-shadow: 0 8px 18px rgba(var(--brand-rgb), 0.18);
    }

    .cliente-modal__action--print {
        color: #0f172a;
        border-color: rgba(15, 23, 42, 0.18);
        background: rgba(15, 23, 42, 0.04);
    }

    .cliente-modal__action--print:hover {
        border-color: rgba(15, 23, 42, 0.30);
        background: rgba(15, 23, 42, 0.08);
    }

    .cliente-modal__action--delete {
        color: #dc2626;
        border-color: rgba(220, 38, 38, 0.25);
        background: rgba(220, 38, 38, 0.08);
    }

    .cliente-modal__action--delete:hover {
        border-color: rgba(220, 38, 38, 0.4);
        background: rgba(220, 38, 38, 0.14);
    }



    @media print {
        body.print-cliente-modal > *:not(.cliente-modal) {
            display: none !important;
        }

        body.print-cliente-modal {
            background: #fff !important;
            color: #0f172a !important;
        }

        body.print-cliente-modal .cliente-modal {
            position: static;
            inset: auto;
            display: block !important;
            padding: 0;
            margin: 0;
            background: #fff;
        }

        body.print-cliente-modal .cliente-modal__backdrop,
        body.print-cliente-modal .cliente-modal__footer,
        body.print-cliente-modal .cliente-modal__close {
            display: none !important;
        }

        body.print-cliente-modal .cliente-modal__dialog {
            width: 100%;
            max-width: 100%;
            max-height: none;
            border: 0;
            border-radius: 0;
            box-shadow: none;
            padding: 0;
            margin: 0;
            overflow: visible;
            background: #fff;
            page-break-inside: avoid;
            break-inside: avoid;
        }

        body.print-cliente-modal .cliente-modal__header,
        body.print-cliente-modal .cliente-modal__content,
        body.print-cliente-modal .cliente-modal__content dt,
        body.print-cliente-modal .cliente-modal__content dd,
        body.print-cliente-modal .cliente-modal__subtitle,
        body.print-cliente-modal .cliente-modal__title {
            color: #0f172a !important;
            opacity: 1 !important;
            -webkit-text-fill-color: #0f172a !important;
        }

        body.print-cliente-modal .cliente-modal__content {
            overflow: visible;
            padding-right: 0;
        }
    }
    @media (max-width: 640px) {
        .cliente-modal {
            padding: 1rem;
        }

        .cliente-modal__dialog {
            border-radius: 18px;
            padding: 1.25rem;
        }

        .cliente-modal__content dl {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
(function () {
    const form = document.querySelector('.cliente-form');
    if (!form) {
        return;
    }
    const pageSizeOptions = <?= json_encode(array_values(array_map('intval', $listPageSizeOptions ?? [15, 20, 30, 40, 50, 100]))); ?>;
    const pageSizeDefault = <?= json_encode((int)($listPageSizeDefault ?? 15)); ?>;
    const paginationTarget = document.querySelector('[data-list-pagination="clientes"]');
    const clientesTbody = document.querySelector('[data-clientes-tbody]');
    const clientesSearch = document.querySelector('[data-clientes-search]');
    const clientesCityFilter = document.querySelector('[data-clientes-city-filter]');
    const clientesCityCombobox = document.querySelector('[data-clientes-city-combobox]');
    const clientesCityToggle = document.querySelector('[data-clientes-city-toggle]');
    const clientesCityOptions = document.querySelector('[data-clientes-city-options]');
    const clientesCityEmpty = document.querySelector('[data-clientes-city-empty]');
    const clientesUfFilter = document.querySelector('[data-clientes-uf-filter]');
    const clientesSortButtons = document.querySelectorAll('[data-clientes-sort-field]');
    const paginationState = {
        page: 1,
        perPage: pageSizeOptions.includes(pageSizeDefault) ? pageSizeDefault : (pageSizeOptions[0] || 15)
    };
    const clientesSortState = {
        field: '',
        direction: ''
    };
    const normalizeClienteFilter = (value) => String(value || '')
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .toLocaleLowerCase('pt-BR')
        .trim();
    const cityOptionButtons = Array.from(document.querySelectorAll('[data-clientes-city-option]'));
    const setCityOptionsOpen = (open) => {
        if (!clientesCityCombobox || !clientesCityOptions || !clientesCityFilter) {
            return;
        }
        clientesCityCombobox.classList.toggle('is-open', open);
        clientesCityOptions.hidden = !open;
        clientesCityFilter.setAttribute('aria-expanded', open ? 'true' : 'false');
    };
    const filterCityOptions = () => {
        const query = normalizeClienteFilter(clientesCityFilter?.value || '');
        let visible = 0;
        cityOptionButtons.forEach((option) => {
            const label = normalizeClienteFilter(option.textContent || '');
            const isAllOption = (option.dataset.cityValue || '') === '';
            const matches = isAllOption ? query === '' : (!query || label.includes(query));
            option.hidden = !matches;
            option.setAttribute('aria-selected', query !== '' && label === query ? 'true' : 'false');
            if (matches) {
                visible++;
            }
        });
        if (clientesCityEmpty) {
            clientesCityEmpty.hidden = visible > 0;
        }
    };
    const updateClientesSortButtons = () => {
        clientesSortButtons.forEach((button) => {
            const isActive = clientesSortState.field === button.dataset.clientesSortField && clientesSortState.direction;
            const indicator = button.querySelector('span');
            button.dataset.direction = isActive ? clientesSortState.direction : '';
            button.setAttribute('aria-sort', isActive ? (clientesSortState.direction === 'asc' ? 'ascending' : 'descending') : 'none');
            if (indicator) {
                indicator.textContent = isActive ? (clientesSortState.direction === 'asc' ? '▲' : '▼') : '⇅';
                indicator.classList.toggle('active', Boolean(isActive));
            }
        });
    };
    const clienteSortValue = (row, field, type) => {
        const value = row.getAttribute(`data-sort-${field}`) || '';
        if (type === 'number') {
            return Number.parseFloat(value) || 0;
        }
        return normalizeClienteFilter(value);
    };
    const applyClientesSort = (rows) => {
        if (!clientesTbody || !clientesSortState.field || !clientesSortState.direction) {
            return rows;
        }

        const field = clientesSortState.field;
        const button = Array.from(clientesSortButtons).find((item) => item.dataset.clientesSortField === field);
        const type = button?.dataset.clientesSortType || 'text';
        const sorted = [...rows].sort((left, right) => {
            const leftValue = clienteSortValue(left, field, type);
            const rightValue = clienteSortValue(right, field, type);
            const result = type === 'number'
                ? leftValue - rightValue
                : String(leftValue).localeCompare(String(rightValue), 'pt-BR', { numeric: true, sensitivity: 'base' });
            return clientesSortState.direction === 'desc' ? -result : result;
        });
        sorted.forEach((row) => clientesTbody.appendChild(row));
        return sorted;
    };
    const applyClientesFilters = () => {
        if (!clientesTbody) {
            return;
        }

        const general = normalizeClienteFilter(clientesSearch?.value || '');
        const city = normalizeClienteFilter(clientesCityFilter?.value || '');
        const uf = String(clientesUfFilter?.value || '').trim().toUpperCase();
        Array.from(clientesTbody.querySelectorAll('tr[data-cliente-row]')).forEach((row) => {
            const matchesGeneral = !general || normalizeClienteFilter(row.dataset.clienteSearch || '').includes(general);
            const matchesCity = !city || normalizeClienteFilter(row.dataset.clienteCity || '').includes(city);
            const matchesUf = !uf || String(row.dataset.clienteUf || '').toUpperCase() === uf;
            row.dataset.clientMatches = matchesGeneral && matchesCity && matchesUf ? '1' : '0';
        });
    };
    const pageWindow = (currentPage, totalPages) => {
        const pages = [];
        const add = (page) => {
            if (page >= 1 && page <= totalPages && !pages.includes(page)) {
                pages.push(page);
            }
        };
        add(1);
        for (let page = currentPage - 2; page <= currentPage + 2; page += 1) {
            add(page);
        }
        add(totalPages);
        pages.sort((left, right) => left - right);
        return pages.reduce((acc, page, index) => {
            if (index > 0 && page - pages[index - 1] > 1) {
                acc.push('...');
            }
            acc.push(page);
            return acc;
        }, []);
    };
    const renderClientesPagination = () => {
        if (!paginationTarget || !clientesTbody) {
            return;
        }
        applyClientesFilters();
        const rows = applyClientesSort(Array.from(clientesTbody.querySelectorAll('tr[data-cliente-row]')));
        const matchedRows = rows.filter((row) => row.dataset.clientMatches !== '0');
        const totalRows = matchedRows.length;
        const totalPages = Math.max(1, Math.ceil(totalRows / paginationState.perPage));
        paginationState.page = Math.min(Math.max(1, paginationState.page), totalPages);
        const startIndex = (paginationState.page - 1) * paginationState.perPage;
        const endIndex = startIndex + paginationState.perPage;
        rows.forEach((row) => {
            const matchedIndex = matchedRows.indexOf(row);
            row.hidden = matchedIndex < startIndex || matchedIndex >= endIndex;
        });
        const start = totalRows ? startIndex + 1 : 0;
        const end = Math.min(totalRows, endIndex);
        const buttons = pageWindow(paginationState.page, totalPages).map((page) => {
            if (page === '...') {
                return '<span class="list-page-ellipsis">...</span>';
            }
            const active = page === paginationState.page;
            return `<button type="button" class="list-page-button ${active ? 'active' : ''}" data-client-page="${page}" aria-current="${active ? 'page' : 'false'}">${page}</button>`;
        }).join('');
        const pageSizeChoices = Array.from(new Set([...pageSizeOptions, paginationState.perPage])).sort((left, right) => left - right);
        const options = pageSizeChoices.map((size) => `<option value="${size}" ${size === paginationState.perPage ? 'selected' : ''}>${size}</option>`).join('');
        paginationTarget.innerHTML = `<span>${start}-${end} de ${totalRows}</span>
            <div class="list-pagination-pages">${buttons}<label>Ir para <input class="list-page-input" type="number" min="1" max="${totalPages}" value="${paginationState.page}" data-client-page-input></label></div>
            <label class="list-pagination-size"><span>Mostrar</span><select class="list-page-size-select" data-client-page-size>${options}</select><input class="list-page-input" type="number" min="1" max="500" value="${paginationState.perPage}" data-client-page-size-manual aria-label="Quantidade manual por página"><span>por página</span></label>`;
    };
    paginationTarget?.addEventListener('click', (event) => {
        const button = event.target instanceof Element ? event.target.closest('[data-client-page]') : null;
        if (!button) {
            return;
        }
        paginationState.page = Number(button.dataset.clientPage || 1);
        renderClientesPagination();
    });
    paginationTarget?.addEventListener('change', (event) => {
        const target = event.target;
        if (!(target instanceof HTMLInputElement || target instanceof HTMLSelectElement)) {
            return;
        }
        if (target.matches('[data-client-page-size]')) {
            paginationState.perPage = Number(target.value) || paginationState.perPage;
            paginationState.page = 1;
            renderClientesPagination();
        }
        if (target.matches('[data-client-page-size-manual]')) {
            paginationState.perPage = Math.min(500, Math.max(1, Number(target.value) || paginationState.perPage));
            paginationState.page = 1;
            renderClientesPagination();
        }
        if (target.matches('[data-client-page-input]')) {
            paginationState.page = Number(target.value) || 1;
            renderClientesPagination();
        }
    });
    paginationTarget?.addEventListener('keydown', (event) => {
        const target = event.target;
        if (event.key !== 'Enter' || !(target instanceof HTMLInputElement) || !target.matches('[data-client-page-input], [data-client-page-size-manual]')) {
            return;
        }
        event.preventDefault();
        if (target.matches('[data-client-page-size-manual]')) {
            paginationState.perPage = Math.min(500, Math.max(1, Number(target.value) || paginationState.perPage));
            paginationState.page = 1;
            renderClientesPagination();
            return;
        }
        paginationState.page = Number(target.value) || 1;
        renderClientesPagination();
    });
    [clientesSearch].forEach((input) => {
        input?.addEventListener('input', () => {
            paginationState.page = 1;
            renderClientesPagination();
        });
    });
    clientesCityFilter?.addEventListener('focus', () => {
        filterCityOptions();
        setCityOptionsOpen(true);
    });
    clientesCityFilter?.addEventListener('input', () => {
        filterCityOptions();
        setCityOptionsOpen(true);
        paginationState.page = 1;
        renderClientesPagination();
    });
    clientesCityFilter?.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            setCityOptionsOpen(false);
            clientesCityFilter.blur();
            return;
        }
        if (event.key === 'ArrowDown') {
            event.preventDefault();
            filterCityOptions();
            setCityOptionsOpen(true);
            cityOptionButtons.find((option) => !option.hidden)?.focus();
        }
    });
    clientesCityToggle?.addEventListener('click', () => {
        const shouldOpen = clientesCityOptions?.hidden !== false;
        filterCityOptions();
        setCityOptionsOpen(shouldOpen);
        if (shouldOpen) {
            clientesCityFilter?.focus();
        }
    });
    clientesCityOptions?.addEventListener('click', (event) => {
        const option = event.target instanceof Element ? event.target.closest('[data-clientes-city-option]') : null;
        if (!option || option.hidden || !clientesCityFilter) {
            return;
        }
        clientesCityFilter.value = option.dataset.cityValue === '' ? '' : (option.textContent || '').trim();
        clientesCityFilter.dispatchEvent(new Event('input', { bubbles: true }));
        setCityOptionsOpen(false);
    });
    clientesCityOptions?.addEventListener('keydown', (event) => {
        if (!['ArrowDown', 'ArrowUp', 'Escape'].includes(event.key)) {
            return;
        }
        event.preventDefault();
        if (event.key === 'Escape') {
            setCityOptionsOpen(false);
            clientesCityFilter?.focus();
            return;
        }
        const visibleOptions = cityOptionButtons.filter((option) => !option.hidden);
        const currentIndex = visibleOptions.indexOf(document.activeElement);
        const offset = event.key === 'ArrowDown' ? 1 : -1;
        const nextIndex = Math.min(visibleOptions.length - 1, Math.max(0, currentIndex + offset));
        visibleOptions[nextIndex]?.focus();
    });
    document.addEventListener('click', (event) => {
        if (clientesCityCombobox && event.target instanceof Node && !clientesCityCombobox.contains(event.target)) {
            setCityOptionsOpen(false);
        }
    });
    [clientesCityFilter, clientesUfFilter].forEach((filter) => {
        filter?.addEventListener('change', () => {
            paginationState.page = 1;
            renderClientesPagination();
        });
    });
    clientesSortButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const field = button.dataset.clientesSortField || '';
            if (!field) {
                return;
            }

            clientesSortState.direction = clientesSortState.field === field && clientesSortState.direction === 'asc' ? 'desc' : 'asc';
            clientesSortState.field = field;
            paginationState.page = 1;
            updateClientesSortButtons();
            renderClientesPagination();
        });
    });
    updateClientesSortButtons();
    renderClientesPagination();
    const hiddenField = form.querySelector('input[name="documento_tipo"]');
    const cnpjButton = form.querySelector('#btn-cnpj');
    const cpfButton = form.querySelector('#btn-cpf');
    const docButtons = form.querySelectorAll('[data-doc-target]');
    const docLabel = form.querySelector('#documento-label');
    const docLabelText = form.querySelector('[data-doc-label-text]');
    const docLabelIcon = form.querySelector('[data-doc-label-icon]');
    const docInput = form.querySelector('#documento');
    const documentConfig = {
        cnpj: {
            label: 'CNPJ',
            placeholder: '00.000.000/0000-00',
            maxLength: 18,
            icon: 'building',
        },
        cpf: {
            label: 'CPF',
            placeholder: '000.000.000-00',
            maxLength: 14,
            icon: 'user',
        },
    };

    const setType = (type, shouldFocus = false) => {
        const resolvedType = type === 'cpf' ? 'cpf' : 'cnpj';
        const config = documentConfig[resolvedType];
        if (!config || !docInput || !docLabel || !hiddenField) {
            return;
        }

        hiddenField.value = resolvedType;

        if (cnpjButton) {
            cnpjButton.classList.toggle('active', resolvedType === 'cnpj');
            cnpjButton.setAttribute('aria-pressed', resolvedType === 'cnpj' ? 'true' : 'false');
        }
        if (cpfButton) {
            cpfButton.classList.toggle('active', resolvedType === 'cpf');
            cpfButton.setAttribute('aria-pressed', resolvedType === 'cpf' ? 'true' : 'false');
        }

        if (docLabelText) {
            docLabelText.textContent = config.label;
        } else {
            docLabel.textContent = config.label;
        }

        if (docLabelIcon) {
            docLabelIcon.setAttribute('data-lucide', config.icon);
            if (window.lucide) {
                window.lucide.createIcons({ elements: [docLabelIcon] });
            }
        }

        docInput.placeholder = config.placeholder;
        docInput.maxLength = config.maxLength;
        docInput.value = '';

        if (shouldFocus) {
            docInput.focus();
        }
    };
    window.setClienteDocumentoType = setType;

    docButtons.forEach((button) => {
        button.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();
            const nextType = button.dataset.docTarget === 'cpf' ? 'cpf' : 'cnpj';
            setType(nextType, true);
        });

        button.addEventListener('touchstart', (event) => {
            event.preventDefault();
            const nextType = button.dataset.docTarget === 'cpf' ? 'cpf' : 'cnpj';
            setType(nextType, false);
        }, { passive: false });
    });

    const maskDigits = (digits, pattern) => {
        let result = '';
        let index = 0;
        for (let i = 0; i < pattern.length; i += 1) {
            if (pattern[i] === '#') {
                if (index >= digits.length) {
                    break;
                }
                result += digits[index];
                index += 1;
            } else {
                if (index >= digits.length) {
                    break;
                }
                result += pattern[i];
            }
        }
        return result;
    };

    const formatDocumentValue = (value, type) => {
        const digits = String(value || '').replace(/\D+/g, '');
        if (type === 'cpf') {
            return maskDigits(digits.slice(0, 11), '###.###.###-##');
        }
        return maskDigits(digits.slice(0, 14), '##.###.###/####-##');
    };

    const applyDocumentoMask = () => {
        if (!docInput) {
            return;
        }
        const currentType = hiddenField && hiddenField.value === 'cpf' ? 'cpf' : 'cnpj';
        const digits = docInput.value.replace(/\D+/g, '');
        const maxDigits = currentType === 'cpf' ? 11 : 14;
        const trimmed = digits.slice(0, maxDigits);
        const pattern = currentType === 'cpf' ? '###.###.###-##' : '##.###.###/####-##';
        docInput.value = maskDigits(trimmed, pattern);
    };

    if (docInput) {
        docInput.addEventListener('input', applyDocumentoMask);
        docInput.addEventListener('keyup', applyDocumentoMask);
        docInput.addEventListener('blur', applyDocumentoMask);
        docInput.addEventListener('paste', () => {
            window.setTimeout(applyDocumentoMask, 0);
        });
    }

    const cepInput = form.querySelector('input[name="cep"]');
    const statusEl = form.querySelector('[data-cep-status]');
    const addressFields = {
        endereco: form.querySelector('input[name="endereco"]'),
        bairro: form.querySelector('input[name="bairro"]'),
        cidade: form.querySelector('input[name="cidade"]'),
        estado: form.querySelector('input[name="estado"]'),
    };
    const ufFlagEl = form.querySelector('[data-uf-flag]');
    const validUFs = new Set(['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO']);

    const ufFlagMap = {
        AC: { nome: 'Acre', url: 'https://commons.wikimedia.org/wiki/Special:FilePath/Bandeira%20do%20Acre.svg' },
        AL: { nome: 'Alagoas', url: 'https://commons.wikimedia.org/wiki/Special:FilePath/Bandeira%20de%20Alagoas.svg' },
        AP: { nome: 'Amapá', url: 'https://commons.wikimedia.org/wiki/Special:FilePath/Bandeira%20do%20Amap%C3%A1.svg' },
        AM: { nome: 'Amazonas', url: 'https://commons.wikimedia.org/wiki/Special:FilePath/Bandeira%20do%20Amazonas.svg' },
        BA: { nome: 'Bahia', url: 'https://commons.wikimedia.org/wiki/Special:FilePath/Bandeira%20da%20Bahia.svg' },
        CE: { nome: 'Ceará', url: 'https://commons.wikimedia.org/wiki/Special:FilePath/Bandeira%20do%20Cear%C3%A1.svg' },
        DF: { nome: 'Distrito Federal', url: 'https://commons.wikimedia.org/wiki/Special:FilePath/Bandeira%20do%20Distrito%20Federal%20%28Brasil%29.svg' },
        ES: { nome: 'Espírito Santo', url: 'https://commons.wikimedia.org/wiki/Special:FilePath/Bandeira%20do%20Esp%C3%ADrito%20Santo.svg' },
        GO: { nome: 'Goiás', url: 'https://commons.wikimedia.org/wiki/Special:FilePath/Bandeira%20de%20Goi%C3%A1s.svg' },
        MA: { nome: 'Maranhão', url: 'https://commons.wikimedia.org/wiki/Special:FilePath/Bandeira%20do%20Maranh%C3%A3o.svg' },
        MT: { nome: 'Mato Grosso', url: 'https://commons.wikimedia.org/wiki/Special:FilePath/Bandeira%20de%20Mato%20Grosso.svg' },
        MS: { nome: 'Mato Grosso do Sul', url: 'https://commons.wikimedia.org/wiki/Special:FilePath/Bandeira%20de%20Mato%20Grosso%20do%20Sul.svg' },
        MG: { nome: 'Minas Gerais', url: 'https://commons.wikimedia.org/wiki/Special:FilePath/Bandeira%20de%20Minas%20Gerais.svg' },
        PA: { nome: 'Pará', url: 'https://commons.wikimedia.org/wiki/Special:FilePath/Bandeira%20do%20Par%C3%A1.svg' },
        PB: { nome: 'Paraíba', url: 'https://commons.wikimedia.org/wiki/Special:FilePath/Bandeira%20da%20Para%C3%ADba.svg' },
        PR: { nome: 'Paraná', url: 'https://commons.wikimedia.org/wiki/Special:FilePath/Bandeira%20do%20Paran%C3%A1.svg' },
        PE: { nome: 'Pernambuco', url: 'https://commons.wikimedia.org/wiki/Special:FilePath/Bandeira%20de%20Pernambuco.svg' },
        PI: { nome: 'Piauí', url: 'https://commons.wikimedia.org/wiki/Special:FilePath/Bandeira%20do%20Piau%C3%AD.svg' },
        RJ: { nome: 'Rio de Janeiro', url: 'https://commons.wikimedia.org/wiki/Special:FilePath/Bandeira%20do%20estado%20do%20Rio%20de%20Janeiro.svg' },
        RN: { nome: 'Rio Grande do Norte', url: 'https://commons.wikimedia.org/wiki/Special:FilePath/Bandeira%20do%20Rio%20Grande%20do%20Norte.svg' },
        RS: { nome: 'Rio Grande do Sul', url: 'https://commons.wikimedia.org/wiki/Special:FilePath/Bandeira%20do%20Rio%20Grande%20do%20Sul.svg' },
        RO: { nome: 'Rondônia', url: 'https://commons.wikimedia.org/wiki/Special:FilePath/Bandeira%20de%20Rond%C3%B4nia.svg' },
        RR: { nome: 'Roraima', url: 'https://commons.wikimedia.org/wiki/Special:FilePath/Bandeira%20de%20Roraima.svg' },
        SC: { nome: 'Santa Catarina', url: 'https://commons.wikimedia.org/wiki/Special:FilePath/Bandeira%20de%20Santa%20Catarina.svg' },
        SP: { nome: 'São Paulo', url: 'https://commons.wikimedia.org/wiki/Special:FilePath/Bandeira%20do%20estado%20de%20S%C3%A3o%20Paulo.svg' },
        SE: { nome: 'Sergipe', url: 'https://commons.wikimedia.org/wiki/Special:FilePath/Bandeira%20de%20Sergipe.svg' },
        TO: { nome: 'Tocantins', url: 'https://commons.wikimedia.org/wiki/Special:FilePath/Bandeira%20do%20Tocantins.svg' },
    };

    const updateUfFlag = () => {
        if (!ufFlagEl || !addressFields.estado) {
            return;
        }
        const uf = addressFields.estado.value.replace(/[^A-Za-z]/g, '').toUpperCase().slice(0, 2);
        if (!validUFs.has(uf)) {
            ufFlagEl.setAttribute('hidden', 'hidden');
            ufFlagEl.removeAttribute('src');
            return;
        }
        const data = ufFlagMap[uf];
        if (!data) {
            ufFlagEl.setAttribute('hidden', 'hidden');
            ufFlagEl.removeAttribute('src');
            return;
        }
        ufFlagEl.src = data.url;
        ufFlagEl.alt = `Bandeira de ${data.nome}`;
        ufFlagEl.removeAttribute('hidden');
    };

    window.updateClienteUfFlag = updateUfFlag;

    if (addressFields.estado) {
        addressFields.estado.addEventListener('input', (event) => {
            const value = event.target.value.replace(/[^A-Za-z]/g, '').toUpperCase().slice(0, 2);
            event.target.value = value;
            updateUfFlag();
        });
        addressFields.estado.addEventListener('blur', updateUfFlag);
    }

    const showStatus = (message, type = 'muted') => {
        if (!statusEl) {
            return;
        }
        statusEl.textContent = message || '';
        statusEl.dataset.state = type;
    };

    const fillAddress = (payload) => {
        if (!payload) {
            return;
        }
        if (addressFields.endereco && payload.logradouro) {
            addressFields.endereco.value = payload.logradouro;
        }
        if (addressFields.bairro && payload.bairro) {
            addressFields.bairro.value = payload.bairro;
        }
        if (addressFields.cidade && payload.localidade) {
            addressFields.cidade.value = payload.localidade;
        }
        if (addressFields.estado && payload.uf) {
            addressFields.estado.value = payload.uf;
            updateUfFlag();
        }
    };

    const normaliseCep = (value) => {
        const digits = (value || '').replace(/\D+/g, '');
        if (digits.length === 8) {
            return `${digits.substring(0, 5)}-${digits.substring(5)}`;
        }
        return digits;
    };

    const fetchCep = (value) => {
        const digits = (value || '').replace(/\D+/g, '');
        if (digits.length !== 8) {
            showStatus('Digite um CEP com 8 dígitos para buscar automaticamente.', 'muted');
            return;
        }

        showStatus('Consultando CEP...', 'muted');
        fetch(`https://viacep.com.br/ws/${digits}/json/`)
            .then((response) => response.ok ? response.json() : Promise.reject())
            .then((data) => {
                if (!data || data.erro) {
                    showStatus('CEP não encontrado. Preencha os campos manualmente.', 'error');
                    return;
                }
                fillAddress(data);
                showStatus('Endereço preenchido automaticamente.', 'success');
            })
            .catch(() => {
                showStatus('Não foi possível consultar o CEP agora.', 'error');
            });
    };

    if (cepInput) {
        cepInput.addEventListener('blur', (event) => {
            const formatted = normaliseCep(event.target.value);
            event.target.value = formatted;
            fetchCep(formatted);
        });

        cepInput.addEventListener('input', (event) => {
            showStatus('', 'muted');
            const digits = event.target.value.replace(/\D+/g, '').slice(0, 8);
            if (digits.length <= 5) {
                event.target.value = digits;
            } else {
                event.target.value = `${digits.substring(0, 5)}-${digits.substring(5)}`;
            }
        });
    }

    setType(hiddenField && hiddenField.value === 'cpf' ? 'cpf' : 'cnpj');
    updateUfFlag();
})();

(function () {
    const modal = document.querySelector('[data-modal]');
    if (!modal) {
        return;
    }

    const form = document.querySelector('.cliente-form');
    const editCancel = form ? form.querySelector('[data-edit-cancel]') : null;
    const submitButton = form ? form.querySelector('button[type="submit"]') : null;
    const cnpjButton = form ? form.querySelector('#btn-cnpj') : null;
    const cpfButton = form ? form.querySelector('#btn-cpf') : null;
    const closeButtons = modal.querySelectorAll('[data-modal-close]');
    const fields = modal.querySelectorAll('[data-field]');
    const subtitle = modal.querySelector('[data-modal-subtitle]');
    const editButton = modal.querySelector('[data-modal-edit]');
    const deleteButton = modal.querySelector('[data-modal-delete]');
    const printButton = modal.querySelector('[data-modal-print]');
    const baseUrl = modal.dataset.baseUrl || '/clientes';
    const defaultSubmitLabel = submitButton ? submitButton.textContent : 'Salvar cliente';
    let currentData = null;

    const formatDocumento = (value, type) => {
        if (!value) {
            return '-';
        }

        const digits = String(value).replace(/\D+/g, '');
        if (type === 'cpf' && digits.length === 11) {
            return `${digits.slice(0, 3)}.${digits.slice(3, 6)}.${digits.slice(6, 9)}-${digits.slice(9)}`;
        }

        if (type === 'cnpj' && digits.length === 14) {
            return `${digits.slice(0, 2)}.${digits.slice(2, 5)}.${digits.slice(5, 8)}/${digits.slice(8, 12)}-${digits.slice(12)}`;
        }

        return value;
    };

    const applyData = (data) => {
        currentData = data;
        if (data && data.id) {
            modal.dataset.clienteId = data.id;
        }
        const enderecoCompleto = [data.endereco, data.numero].filter(Boolean).join(', ');

        fields.forEach((field) => {
            const key = field.dataset.field;
            let value = data[key];
            if (value === undefined || value === null) {
                value = '';
            }

            if (key === 'cnpj') {
                value = formatDocumento(value, 'cnpj');
            }

            if (key === 'cpf') {
                value = formatDocumento(value, 'cpf');
            }

            if (key === 'endereco') {
                value = enderecoCompleto || '-';
            }

            if (key === 'contato_tipo') {
                const tipo = String(value || '').toLowerCase();
                value = tipo === 'celular' ? 'Celular' : (tipo === 'telefone' ? 'Telefone' : '-');
            }

            field.textContent = value ? String(value) : '-';
        });

        if (subtitle) {
            subtitle.textContent = data.email ? `Contato: ${data.email}` : '';
        }

        modal.removeAttribute('hidden');
        document.body.style.overflow = 'hidden';

        if (window.lucide) {
            window.lucide.createIcons({ elements: modal.querySelectorAll('[data-lucide]') });
        }
    };

    const resetEditState = () => {
        if (!form) {
            return;
        }
        form.removeAttribute('data-edit-id');
        if (submitButton) {
            submitButton.textContent = defaultSubmitLabel;
        }
        if (editCancel) {
            editCancel.hidden = true;
        }
        form.reset();
        const radioNames = new Set();
        form.querySelectorAll('input, textarea, select').forEach((field) => {
            if (field.type === 'radio') {
                if (radioNames.has(field.name)) {
                    return;
                }
                radioNames.add(field.name);
                const group = Array.from(form.querySelectorAll(`input[type="radio"][name="${field.name}"]`));
                const checked = group.find((option) => option.defaultChecked) || group[0];
                group.forEach((option) => {
                    option.checked = option === checked;
                    option.dispatchEvent(new Event('change', { bubbles: true }));
                });
                return;
            }
            if (field.type === 'checkbox') {
                field.checked = field.defaultChecked;
                field.dispatchEvent(new Event('change', { bubbles: true }));
                return;
            }
            if (field.tagName === 'SELECT') {
                field.selectedIndex = 0;
                field.dispatchEvent(new Event('change', { bubbles: true }));
                return;
            }
            field.value = '';
            field.dispatchEvent(new Event('input', { bubbles: true }));
            field.dispatchEvent(new Event('change', { bubbles: true }));
        });
        if (window.updateClienteUfFlag) {
            window.updateClienteUfFlag();
        }
        if (cnpjButton) {
            cnpjButton.click();
        }
    };
    window.resetClienteEditState = resetEditState;

    const hideModal = () => {
        modal.setAttribute('hidden', 'hidden');
        document.body.style.overflow = '';
    };

    closeButtons.forEach((button) => button.addEventListener('click', hideModal));

    modal.addEventListener('click', (event) => {
        if (event.target === modal.querySelector('.cliente-modal__backdrop')) {
            hideModal();
        }
    });

    if (editCancel) {
        editCancel.addEventListener('click', () => {
            resetEditState();
            if (form) {
                form.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    }

    if (editButton) {
        editButton.addEventListener('click', () => {
        if (!form || !currentData) {
            return;
        }

        const documentType = currentData.cpf ? 'cpf' : 'cnpj';
        if (documentType === 'cpf' && cpfButton) {
            cpfButton.click();
        } else if (cnpjButton) {
            cnpjButton.click();
        }

        const fieldsToFill = [
            'razao_social',
            'nome_fantasia',
            'ie',
            'email',
            'telefone',
            'contato_tipo',
            'whatsapp',
            'cep',
            'endereco',
            'numero',
            'bairro',
            'cidade',
            'estado',
        ];

        fieldsToFill.forEach((name) => {
            const groupInputs = form.querySelectorAll(`[name="${name}"]`);
            if (!groupInputs.length) {
                return;
            }

            const rawValue = currentData[name];
            const normalizedValue = rawValue === undefined || rawValue === null ? '' : String(rawValue);

            if (groupInputs[0].type === 'radio') {
                let hasMatch = false;
                groupInputs.forEach((option) => {
                    const checked = option.value === normalizedValue;
                    option.checked = checked;
                    hasMatch = hasMatch || checked;
                });
                if (!hasMatch && groupInputs[0]) {
                    groupInputs[0].checked = true;
                }
                return;
            }

            groupInputs[0].value = normalizedValue;
        });

        if (window.updateClienteUfFlag) {
            window.updateClienteUfFlag();
        }

        const docType = currentData.cpf ? 'cpf' : 'cnpj';
        const docValue = currentData[docType] || '';
        if (window.setClienteDocumentoType) {
            window.setClienteDocumentoType(docType, false);
        }
        const docInput = form.querySelector('#documento');
        if (docInput) {
            window.setTimeout(() => {
                docInput.value = formatDocumentValue(docValue, docType);
            }, 140);
        }

        if (submitButton) {
            submitButton.textContent = 'Atualizar cliente';
        }

        form.dataset.editId = String(currentData.id);
        if (editCancel) {
            editCancel.hidden = false;
        }

        hideModal();
        form.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    }


    if (printButton) {
        printButton.addEventListener('click', () => {
            const dialog = modal.querySelector('.cliente-modal__dialog');
            if (!dialog) {
                return;
            }

            const printableDialog = dialog.cloneNode(true);
            const closeButton = printableDialog.querySelector('.cliente-modal__close');
            const footer = printableDialog.querySelector('.cliente-modal__footer');

            if (closeButton) {
                closeButton.remove();
            }
            if (footer) {
                footer.remove();
            }

            const printWindow = window.open('', '_blank', 'width=900,height=700');
            if (!printWindow) {
                return;
            }

            printWindow.document.open();
            printWindow.document.write(`
                <!doctype html>
                <html lang="pt-BR">
                <head>
                    <meta charset="utf-8">
                    <title>Detalhes do cliente</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 24px; color: #0f172a; }
                        .cliente-modal__dialog { width: 100%; max-width: 100%; border: 0; box-shadow: none; background: #fff; }
                        .cliente-modal__header { margin-bottom: 14px; }
                        .cliente-modal__header h3 { margin: 0 0 6px; font-size: 28px; }
                        .cliente-modal__subtitle { margin: 0; color: #475569; }
                        .cliente-modal__content { overflow: visible; }
                        .cliente-modal__content dl { display: grid; grid-template-columns: repeat(2, minmax(220px, 1fr)); gap: 14px 24px; margin: 0; }
                        .cliente-modal__content dt { font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; margin-bottom: 4px; }
                        .cliente-modal__content dd { margin: 0; font-size: 20px; font-weight: 700; color: #0f172a; word-break: break-word; }
                        @media print { body { margin: 0; } }
                    </style>
                </head>
                <body></body>
                </html>
            `);
            printWindow.document.body.appendChild(printableDialog);
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
            printWindow.close();
        });
    }
    if (deleteButton) {
        deleteButton.addEventListener('click', () => {
        if (!currentData || !currentData.id) {
            return;
        }

        const confirmed = window.confirm('Tem certeza que deseja excluir este cliente?');
        if (!confirmed) {
            return;
        }

        deleteButton.disabled = true;
        const deleteUrl = `${baseUrl.replace(/\/$/, '')}/${currentData.id}`;
        fetch(deleteUrl, {
            method: 'DELETE',
            headers: { 'Accept': 'application/json' },
        })
            .then((response) => response.ok ? response.json() : Promise.reject(response))
            .then(() => {
                window.appLiveSaved?.({ message: 'Cliente excluido com sucesso.', source: 'clientes' });
                modal?.classList.add('hidden');
                modal?.setAttribute('aria-hidden', 'true');
            })
            .catch(() => {
                alert('Não foi possível excluir o cliente agora.');
            })
            .finally(() => {
                deleteButton.disabled = false;
            });
        });
    }

    const validateRequiredFields = () => {
        if (!form) {
            return { valid: true, missing: [] };
        }

        const requiredFields = [
            { name: 'razao_social', label: 'Nome / Razão social' },
            { name: 'nome_fantasia', label: 'Nome fantasia' },
            { name: 'documento', label: 'CNPJ/CPF' },
            { name: 'email', label: 'E-mail' },
            { name: 'telefone', label: 'Contato' },
            { name: 'contato_tipo', label: 'Tipo do contato' },
            { name: 'whatsapp', label: 'WhatsApp' },
            { name: 'cep', label: 'CEP' },
            { name: 'endereco', label: 'Endereço' },
            { name: 'numero', label: 'Número' },
            { name: 'bairro', label: 'Bairro' },
            { name: 'cidade', label: 'Cidade' },
            { name: 'estado', label: 'Estado (UF)' },
        ];

        const missing = [];
        let firstField = null;

        requiredFields.forEach((fieldMeta) => {
            const candidates = form.querySelectorAll(`[name="${fieldMeta.name}"]`);
            if (!candidates.length) {
                return;
            }

            const firstCandidate = candidates[0];
            let isMissing = false;

            if (firstCandidate.type === 'radio') {
                isMissing = !Array.from(candidates).some((option) => option.checked);
            } else {
                isMissing = String(firstCandidate.value || '').trim() === '';
            }

            if (isMissing) {
                missing.push(fieldMeta);
                if (!firstField) {
                    firstField = firstCandidate;
                }
            }
        });

        if (missing.length) {
            const missingLabels = missing.map((item) => item.label).join(', ');
            alert(`Preencha os campos obrigatórios: ${missingLabels}.`);
            if (firstField && typeof firstField.focus === 'function') {
                firstField.focus();
            }
            return { valid: false, missing };
        }

        return { valid: true, missing: [] };
    };

    if (form) {
        form.addEventListener('submit', (event) => {
        const validation = validateRequiredFields();
        if (!validation.valid) {
            event.preventDefault();
            return;
        }

        const editId = form.dataset.editId;
        if (!editId) {
            return;
        }

        event.preventDefault();
        const payload = Object.fromEntries(new FormData(form).entries());

        if (submitButton) {
            submitButton.disabled = true;
        }

        const updateUrl = `${baseUrl.replace(/\/$/, '')}/${editId}`;
        fetch(updateUrl, {
            method: 'PUT',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(payload),
        })
            .then((response) => response.ok ? response.json() : Promise.reject(response))
            .then(() => {
                window.appLiveSaved?.({ message: 'Cliente atualizado com sucesso.', source: 'clientes' });
                modal?.classList.add('hidden');
                modal?.setAttribute('aria-hidden', 'true');
            })
            .catch(() => {
                alert('Não foi possível atualizar o cliente agora.');
            })
            .finally(() => {
                if (submitButton) {
                    submitButton.disabled = false;
                }
            });
        });
    }

    document.addEventListener('click', (event) => {
        const target = event.target;
        if (!(target instanceof Element)) {
            return;
        }

        const button = target.closest('.js-cliente-detalhe');
        if (!button) {
            return;
        }

        const payload = button.getAttribute('data-cliente');
        if (payload) {
            try {
                const parsed = JSON.parse(payload);
                applyData(parsed);
                return;
            } catch (error) {
                // segue para fallback via endpoint
            }
        }

        const endpoint = button.getAttribute('data-endpoint');
        if (!endpoint) {
            return;
        }

        button.disabled = true;
        button.classList.add('is-loading');

        fetch(endpoint, {
            headers: { 'Accept': 'application/json' }
        })
            .then((response) => response.ok ? response.json() : Promise.reject(response))
            .then((data) => {
                applyData(data);
            })
            .catch(() => {
                alert('Não foi possível carregar os detalhes deste cliente agora.');
            })
            .finally(() => {
                button.disabled = false;
                button.classList.remove('is-loading');
            });
    });
})();
</script>
