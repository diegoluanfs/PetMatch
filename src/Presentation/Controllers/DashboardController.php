<?php

declare(strict_types=1);

namespace PetMatch\Presentation\Controllers;

use PetMatch\Infrastructure\Http\Request;
use PetMatch\Presentation\Responses\HtmlResponse;

final class DashboardController
{
    public function __invoke(Request $request): HtmlResponse
    {
        return HtmlResponse::ok($this->render());
    }

    private function render(): string
    {
        return <<<'HTML'
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PetMatch Lab</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&display=swap');

        :root {
            color-scheme: light;
            --bg: #f6f1e8;
            --bg-alt: #eef4f1;
            --surface: rgba(255, 255, 255, 0.82);
            --surface-strong: #ffffff;
            --text: #102019;
            --muted: #5b6b63;
            --line: rgba(16, 32, 25, 0.12);
            --accent: #0f766e;
            --accent-strong: #115e59;
            --warm: #c97335;
            --danger: #b42318;
            --shadow: 0 24px 70px rgba(16, 32, 25, 0.12);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Space Grotesk', 'Trebuchet MS', sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at top left, rgba(15, 118, 110, 0.16), transparent 28%),
                radial-gradient(circle at top right, rgba(201, 115, 53, 0.14), transparent 24%),
                linear-gradient(180deg, var(--bg) 0%, #fbfaf6 60%, #f3f7f3 100%);
        }

        .shell {
            width: min(1240px, calc(100% - 32px));
            margin: 0 auto;
            padding: 28px 0 48px;
        }

        .hero {
            position: relative;
            overflow: hidden;
            border: 1px solid var(--line);
            border-radius: 28px;
            padding: 28px;
            background: linear-gradient(135deg, rgba(16, 32, 25, 0.96), rgba(17, 94, 89, 0.96));
            color: #f7f9f8;
            box-shadow: var(--shadow);
        }

        .hero::after {
            content: '';
            position: absolute;
            inset: auto -10% -45% auto;
            width: 320px;
            height: 320px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.18), transparent 70%);
            pointer-events: none;
        }

        .hero-top {
            display: flex;
            gap: 16px;
            align-items: flex-start;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.11);
            color: #dce9e6;
            font-size: 12px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        h1 {
            margin: 14px 0 10px;
            font-size: clamp(34px, 6vw, 58px);
            line-height: 0.95;
            letter-spacing: -0.05em;
            max-width: 12ch;
        }

        .hero p {
            max-width: 60ch;
            margin: 0;
            color: rgba(247, 249, 248, 0.84);
            font-size: 16px;
            line-height: 1.6;
        }

        .hero-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 18px;
        }

        .button, button {
            appearance: none;
            border: 0;
            border-radius: 14px;
            padding: 12px 16px;
            font: inherit;
            font-weight: 700;
            cursor: pointer;
            transition: transform 140ms ease, box-shadow 140ms ease, background 140ms ease, opacity 140ms ease;
        }

        button:hover, .button:hover {
            transform: translateY(-1px);
        }

        .button-primary {
            background: #f7f9f8;
            color: var(--accent-strong);
            box-shadow: 0 14px 34px rgba(0, 0, 0, 0.18);
        }

        .button-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: #f7f9f8;
            border: 1px solid rgba(255, 255, 255, 0.18);
        }

        .button-danger {
            background: rgba(180, 35, 24, 0.12);
            color: #ffd6d1;
            border: 1px solid rgba(180, 35, 24, 0.18);
        }

        .grid {
            display: grid;
            grid-template-columns: 360px 1fr;
            gap: 20px;
            margin-top: 20px;
        }

        .panel {
            background: var(--surface);
            backdrop-filter: blur(20px);
            border: 1px solid var(--line);
            border-radius: 24px;
            box-shadow: var(--shadow);
        }

        .panel-head {
            padding: 20px 20px 0;
        }

        .panel-head h2 {
            margin: 0;
            font-size: 20px;
            letter-spacing: -0.03em;
        }

        .panel-head p {
            margin: 8px 0 0;
            color: var(--muted);
            line-height: 1.55;
        }

        .panel-body {
            padding: 20px;
        }

        .stack {
            display: grid;
            gap: 12px;
        }

        label {
            display: grid;
            gap: 6px;
            font-size: 13px;
            color: var(--muted);
        }

        input, textarea, select {
            width: 100%;
            padding: 12px 14px;
            border-radius: 14px;
            border: 1px solid rgba(16, 32, 25, 0.12);
            background: rgba(255, 255, 255, 0.9);
            color: var(--text);
            font: inherit;
            outline: none;
        }

        textarea {
            min-height: 96px;
            resize: vertical;
        }

        input:focus, textarea:focus, select:focus {
            border-color: rgba(15, 118, 110, 0.5);
            box-shadow: 0 0 0 4px rgba(15, 118, 110, 0.11);
        }

        .status {
            display: grid;
            gap: 8px;
            padding: 14px;
            border-radius: 18px;
            background: rgba(15, 118, 110, 0.08);
            color: var(--text);
            border: 1px solid rgba(15, 118, 110, 0.16);
        }

        .status strong {
            font-size: 14px;
        }

        .status small {
            color: var(--muted);
            line-height: 1.5;
        }

        .workspace {
            display: grid;
            gap: 20px;
        }

        .toolbar {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
        }

        .pill-row {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .pill {
            padding: 10px 14px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.76);
            border: 1px solid var(--line);
            color: var(--muted);
            font-size: 13px;
        }

        .pet-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 16px;
        }

        .pet-card {
            display: grid;
            gap: 14px;
            padding: 18px;
            border-radius: 22px;
            background: var(--surface-strong);
            border: 1px solid var(--line);
            box-shadow: 0 12px 30px rgba(16, 32, 25, 0.08);
        }

        .pet-card h3 {
            margin: 0;
            font-size: 20px;
            letter-spacing: -0.03em;
        }

        .pet-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            color: var(--muted);
            font-size: 13px;
        }

        .tag {
            padding: 7px 10px;
            border-radius: 999px;
            background: rgba(15, 118, 110, 0.08);
            color: var(--accent-strong);
        }

        .tag.is-archived {
            background: rgba(180, 35, 24, 0.08);
            color: var(--danger);
        }

        .photo-list {
            display: grid;
            gap: 10px;
        }

        .photo-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 10px 12px;
            border-radius: 16px;
            border: 1px solid rgba(16, 32, 25, 0.08);
            background: rgba(246, 241, 232, 0.7);
        }

        .photo-row code {
            word-break: break-all;
            color: var(--accent-strong);
        }

        .muted {
            color: var(--muted);
        }

        .split {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .notice {
            padding: 14px 16px;
            border-radius: 18px;
            background: rgba(201, 115, 53, 0.1);
            color: #7b4318;
            border: 1px solid rgba(201, 115, 53, 0.18);
        }

        .log {
            min-height: 64px;
            padding: 14px 16px;
            border-radius: 18px;
            background: #0f172a;
            color: #d8e4ff;
            white-space: pre-wrap;
            line-height: 1.5;
        }

        .empty {
            padding: 20px;
            border: 1px dashed rgba(16, 32, 25, 0.16);
            border-radius: 22px;
            color: var(--muted);
            background: rgba(255, 255, 255, 0.55);
        }

        @media (max-width: 980px) {
            .grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 720px) {
            .shell {
                width: min(100% - 20px, 1240px);
                padding-top: 12px;
            }

            .hero, .panel, .pet-card {
                border-radius: 20px;
            }

            .split {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="shell">
        <section class="hero">
            <div class="hero-top">
                <div>
                    <div class="eyebrow">PetMatch Lab</div>
                    <h1>Uma interface rápida para testar o produto.</h1>
                    <p>
                        Use esta página para autenticar, criar pets, anexar fotos, arquivar registros e avaliar o fluxo real
                        sem depender de ferramenta externa. A ideia é mostrar o estado atual da aplicação com o mínimo de atrito.
                    </p>
                    <div class="hero-actions">
                        <button class="button button-primary" type="button" id="refreshPets">Atualizar pets</button>
                        <a class="button button-secondary" href="/health">Ver health</a>
                    </div>
                </div>
                <div class="notice">
                    Credenciais de teste locais:
                    <strong>ong.admin@example.com / secret123</strong>
                </div>
            </div>
        </section>

        <div class="grid">
            <aside class="workspace">
                <section class="panel">
                    <div class="panel-head">
                        <h2>Autenticação</h2>
                        <p>Faça login com uma conta de organização para testar criação, arquivamento e fotos.</p>
                    </div>
                    <div class="panel-body stack">
                        <div class="status" id="sessionStatus">
                            <strong>Sessão não consultada</strong>
                            <small>Clique em "Ver sessão" depois de autenticar para inspecionar o usuário atual.</small>
                        </div>
                        <label>
                            E-mail
                            <input id="email" type="email" value="ong.admin@example.com" autocomplete="email">
                        </label>
                        <label>
                            Senha
                            <input id="password" type="password" value="secret123" autocomplete="current-password">
                        </label>
                        <div class="hero-actions" style="margin-top: 4px;">
                            <button class="button button-primary" type="button" id="loginButton">Entrar</button>
                            <button class="button button-secondary" type="button" id="sessionButton">Ver sessão</button>
                            <button class="button button-danger" type="button" id="logoutButton">Sair</button>
                        </div>
                    </div>
                </section>

                <section class="panel">
                    <div class="panel-head">
                        <h2>Criar pet</h2>
                        <p>Use este formulário para criar um novo pet e já ver o resultado na lista ao lado.</p>
                    </div>
                    <div class="panel-body stack">
                        <div class="split">
                            <label>Nome <input id="petName" type="text" value="Tico"></label>
                            <label>Espécie <input id="petAnimalType" type="text" value="dog"></label>
                        </div>
                        <label>Descrição <textarea id="petDescription">SRD alegre e brincalhão</textarea></label>
                        <div class="split">
                            <label>Raça <input id="petBreed" type="text" value="vira-lata"></label>
                            <label>Sexo <select id="petGender"><option value="male">male</option><option value="female">female</option><option value="unknown">unknown</option></select></label>
                        </div>
                        <div class="split">
                            <label>Nascimento <input id="petBirthDate" type="date" value="2022-03-11"></label>
                            <label>Porte <select id="petSize"><option value="small">small</option><option value="medium" selected>medium</option><option value="large">large</option><option value="extra_large">extra_large</option></select></label>
                        </div>
                        <div class="split">
                            <label>Cidade <input id="petCity" type="text" value="Santa Maria"></label>
                            <label>Estado <input id="petState" type="text" value="RS" maxlength="2"></label>
                        </div>
                        <label>Latitude <input id="petLatitude" type="text" placeholder="opcional"></label>
                        <label>Longitude <input id="petLongitude" type="text" placeholder="opcional"></label>
                        <button class="button button-primary" type="button" id="createPetButton">Criar pet</button>
                    </div>
                </section>

                <section class="panel">
                    <div class="panel-head">
                        <h2>Log de ações</h2>
                        <p>As respostas da API aparecem aqui para facilitar a avaliação manual.</p>
                    </div>
                    <div class="panel-body">
                        <div class="log" id="actionLog">Pronto para uso.</div>
                    </div>
                </section>
            </aside>

            <main class="workspace">
                <section class="panel">
                    <div class="panel-head">
                        <div class="toolbar">
                            <div>
                                <h2>Pets</h2>
                                <p>Cartões vindos do endpoint público. Remover um pet do catálogo apenas o arquiva para preservar o histórico.</p>
                            </div>
                            <div class="pill-row">
                                <div class="pill" id="petCountPill">0 pets</div>
                                <div class="pill">Fotos anexadas por metadata</div>
                                <div class="pill">Arquivamento disponível</div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="pet-grid" id="petGrid"></div>
                    </div>
                </section>

                <section class="panel">
                    <div class="panel-head">
                        <div class="toolbar">
                            <div>
                                <h2>Solicitações de adoção</h2>
                                <p>Revise os pedidos recebidos para os pets da sua organização.</p>
                            </div>
                            <button class="button button-secondary" type="button" id="refreshRequests">Atualizar solicitações</button>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="pet-grid" id="requestGrid"></div>
                    </div>
                </section>
            </main>
        </div>
    </div>

    <script>
        const state = {
            me: null,
            pets: [],
            requests: [],
        };

        const elements = {
            sessionStatus: document.getElementById('sessionStatus'),
            petGrid: document.getElementById('petGrid'),
            petCountPill: document.getElementById('petCountPill'),
            actionLog: document.getElementById('actionLog'),
            refreshPets: document.getElementById('refreshPets'),
            loginButton: document.getElementById('loginButton'),
            sessionButton: document.getElementById('sessionButton'),
            logoutButton: document.getElementById('logoutButton'),
            createPetButton: document.getElementById('createPetButton'),
            requestGrid: document.getElementById('requestGrid'),
            refreshRequests: document.getElementById('refreshRequests'),
        };

        function escapeHtml(value) {
            return String(value)
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        function writeLog(message) {
            elements.actionLog.textContent = message;
        }

        async function api(path, options = {}) {
            const isFormData = typeof FormData !== 'undefined' && options.body instanceof FormData;
            const response = await fetch(path, {
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    ...(isFormData ? {} : { 'Content-Type': 'application/json' }),
                    ...(options.headers || {}),
                },
                ...options,
            });

            const raw = await response.text();
            let payload = null;

            if (raw) {
                try {
                    payload = JSON.parse(raw);
                } catch (error) {
                    payload = raw;
                }
            }

            if (!response.ok) {
                const message = payload && typeof payload === 'object' && payload.error ? payload.error : `HTTP ${response.status}`;
                throw new Error(message);
            }

            return payload;
        }

        function renderSession() {
            if (!state.me || !state.me.data) {
                elements.sessionStatus.innerHTML = '<strong>Sem sessão ativa</strong><small>Faça login para habilitar as ações de organização.</small>';
                return;
            }

            const user = state.me.data;
            elements.sessionStatus.innerHTML = `
                <strong>${escapeHtml(user.name)} · ${escapeHtml(user.role)}</strong>
                <small>${escapeHtml(user.email)}<br>organization_id: ${user.organization_id ?? 'n/a'}</small>
            `;
        }

        function renderPets() {
            elements.petCountPill.textContent = `${state.pets.length} pets`;

            if (!state.pets.length) {
                elements.petGrid.innerHTML = '<div class="empty">Nenhum pet encontrado. Crie um novo pet para começar a testar a interface.</div>';
                return;
            }

            elements.petGrid.innerHTML = state.pets.map((pet) => {
                const photos = Array.isArray(pet.photos) ? pet.photos : [];
                const photoMarkup = photos.length
                    ? photos.map((photo) => `
                        <div class="photo-row">
                            <div style="display:flex; align-items:center; gap:12px; min-width:0; flex:1;">
                                <img src="${escapeHtml(photo.path)}" alt="Foto do pet ${escapeHtml(pet.name)}" style="width:60px; height:60px; border-radius:12px; object-fit:cover; border: 1px solid rgba(16,32,25,0.08); background:#eef2f0;" />
                                <div style="min-width:0; overflow:hidden;">
                                    <strong>#${photo.sort_order}</strong>
                                    <div class="muted"><code>${escapeHtml(photo.path)}</code></div>
                                </div>
                            </div>
                            <button class="button button-danger" type="button" data-delete-photo="${pet.id}:${photo.id}">Remover</button>
                        </div>
                    `).join('')
                    : '<div class="empty" style="padding:12px;">Sem fotos anexadas.</div>';

                const statusClass = pet.status === 'archived' ? 'tag is-archived' : 'tag';
                const archiveButton = pet.status === 'available'
                    ? `<button class="button button-secondary" type="button" data-remove-pet="${pet.id}">Remover do catálogo</button>`
                    : `<button class="button button-secondary" type="button" disabled>Removido do catálogo</button>`;

                return `
                    <article class="pet-card">
                        <div>
                            <h3>${escapeHtml(pet.name)}</h3>
                            <div class="pet-meta">
                                <span class="${statusClass}">${escapeHtml(pet.status)}</span>
                                <span class="tag">${escapeHtml(pet.animal_type)}</span>
                                <span class="tag">${escapeHtml(pet.breed)}</span>
                                <span class="tag">${escapeHtml(pet.city)} / ${escapeHtml(pet.state)}</span>
                            </div>
                        </div>
                        <p class="muted" style="margin: 0; line-height: 1.55;">${escapeHtml(pet.description)}</p>
                        <div class="photo-list">${photoMarkup}</div>
                        <div class="stack">
                            <div class="split">
                                <label>Arquivo da foto
                                    <input type="file" accept="image/*" data-photo-file="${pet.id}">
                                </label>
                                <label>Ordem
                                    <input type="number" data-photo-order="${pet.id}" min="0" value="0">
                                </label>
                            </div>
                            <label>Caminho manual opcional
                                <input type="text" data-photo-path="${pet.id}" placeholder="/storage/pet-photos/arquivo.jpg">
                            </label>
                            <div class="hero-actions" style="margin-top: 0;">
                                <button class="button button-primary" type="button" data-add-photo="${pet.id}">Anexar foto</button>
                                ${archiveButton}
                            </div>
                        </div>
                    </article>
                `;
            }).join('');

            bindPetActions();
        }

        function renderRequests() {
            if (!state.requests.length) {
                elements.requestGrid.innerHTML = '<div class="empty">Nenhuma solicitação recebida para os seus pets.</div>';
                return;
            }

            elements.requestGrid.innerHTML = state.requests.map((request) => {
                const pendingActions = request.status === 'pending'
                    ? `<div class="hero-actions" style="margin-top: 0;"><button class="button button-primary" type="button" data-approve-request="${request.id}">Aprovar</button><button class="button button-danger" type="button" data-reject-request="${request.id}">Rejeitar</button></div>`
                    : '';

                return `<article class="pet-card"><div><h3>${escapeHtml(request.pet_name || `Pet #${request.pet_id}`)}</h3><div class="pet-meta"><span class="tag">${escapeHtml(request.status)}</span><span class="tag">solicitação #${request.id}</span></div></div><p class="muted" style="margin: 0; line-height: 1.55;">${escapeHtml(request.message || 'Sem mensagem enviada.')}</p>${pendingActions}</article>`;
            }).join('');

            bindRequestActions();
        }

        function bindRequestActions() {
            document.querySelectorAll('[data-approve-request], [data-reject-request]').forEach((button) => {
                button.addEventListener('click', async () => {
                    const requestId = button.getAttribute('data-approve-request') || button.getAttribute('data-reject-request');
                    const action = button.hasAttribute('data-approve-request') ? 'approve' : 'reject';

                    try {
                        const result = await api(`/api/v1/adoption-requests/${requestId}/${action}`, { method: 'PATCH' });
                        writeLog(JSON.stringify(result, null, 2));
                        await loadRequests();
                        await loadPets();
                    } catch (error) {
                        writeLog(`Erro ao ${action === 'approve' ? 'aprovar' : 'rejeitar'} solicitação: ${error.message}`);
                    }
                });
            });
        }

        function bindPetActions() {
            document.querySelectorAll('[data-add-photo]').forEach((button) => {
                button.addEventListener('click', async () => {
                    const petId = Number(button.getAttribute('data-add-photo'));
                    const fileInput = document.querySelector(`[data-photo-file="${petId}"]`);
                    const pathInput = document.querySelector(`[data-photo-path="${petId}"]`);
                    const orderInput = document.querySelector(`[data-photo-order="${petId}"]`);

                    try {
                        const payload = new FormData();
                        payload.append('sort_order', String(Number(orderInput.value || 0)));

                        const file = fileInput.files && fileInput.files.length > 0 ? fileInput.files[0] : null;

                        if (file) {
                            payload.append('photo', file);
                        } else {
                            payload.append('path', pathInput.value);
                        }

                        const result = await api(`/api/v1/pets/${petId}/photos`, {
                            method: 'POST',
                            body: payload,
                        });

                        writeLog(JSON.stringify(result, null, 2));
                        await loadPets();
                    } catch (error) {
                        writeLog(`Erro ao anexar foto: ${error.message}`);
                    }
                });
            });

            document.querySelectorAll('[data-delete-photo]').forEach((button) => {
                button.addEventListener('click', async () => {
                    const [petId, photoId] = button.getAttribute('data-delete-photo').split(':');

                    try {
                        const result = await api(`/api/v1/pets/${petId}/photos/${photoId}`, {
                            method: 'DELETE',
                        });

                        writeLog(JSON.stringify(result, null, 2));
                        await loadPets();
                    } catch (error) {
                        writeLog(`Erro ao remover foto: ${error.message}`);
                    }
                });
            });

            document.querySelectorAll('[data-remove-pet]').forEach((button) => {
                button.addEventListener('click', async () => {
                    const petId = button.getAttribute('data-remove-pet');

                    if (!window.confirm('Remover este pet do catálogo? O registro será mantido no banco como arquivado para auditoria.')) {
                        return;
                    }

                    try {
                        const result = await api(`/api/v1/pets/${petId}/archive`, {
                            method: 'PATCH',
                        });

                        writeLog(JSON.stringify(result, null, 2));
                        await loadPets();
                    } catch (error) {
                        writeLog(`Erro ao remover pet do catálogo: ${error.message}`);
                    }
                });
            });
        }

        async function loadSession() {
            try {
                state.me = await api('/api/v1/auth/me', { method: 'GET' });
            } catch (error) {
                state.me = null;
            }

            renderSession();
        }

        async function loadPets() {
            try {
                const result = await api('/api/v1/organizations/pets', { method: 'GET' });
                state.pets = Array.isArray(result.data) ? result.data : [];
                renderPets();
            } catch (error) {
                state.pets = [];
                elements.petGrid.innerHTML = `<div class="empty">Falha ao carregar pets: ${escapeHtml(error.message)}</div>`;
            }
        }

        async function loadRequests() {
            try {
                const result = await api('/api/v1/organizations/adoption-requests', { method: 'GET' });
                state.requests = Array.isArray(result.data) ? result.data : [];
                renderRequests();
            } catch (error) {
                state.requests = [];
                elements.requestGrid.innerHTML = `<div class="empty">Faça login como organização para visualizar solicitações: ${escapeHtml(error.message)}</div>`;
            }
        }

        async function login() {
            try {
                const result = await api('/api/v1/auth/login', {
                    method: 'POST',
                    body: JSON.stringify({
                        email: document.getElementById('email').value,
                        password: document.getElementById('password').value,
                    }),
                });

                writeLog(JSON.stringify(result, null, 2));
                await loadSession();
                await loadPets();
                await loadRequests();
            } catch (error) {
                writeLog(`Erro no login: ${error.message}`);
            }
        }

        async function logout() {
            try {
                const result = await api('/api/v1/auth/logout', { method: 'POST', body: JSON.stringify({}) });
                writeLog(JSON.stringify(result, null, 2));
                await loadSession();
                await loadRequests();
            } catch (error) {
                writeLog(`Erro no logout: ${error.message}`);
            }
        }

        async function createPet() {
            try {
                const result = await api('/api/v1/pets', {
                    method: 'POST',
                    body: JSON.stringify({
                        name: document.getElementById('petName').value,
                        description: document.getElementById('petDescription').value,
                        animal_type: document.getElementById('petAnimalType').value,
                        breed: document.getElementById('petBreed').value,
                        gender: document.getElementById('petGender').value,
                        birth_date: document.getElementById('petBirthDate').value,
                        size: document.getElementById('petSize').value,
                        city: document.getElementById('petCity').value,
                        state: document.getElementById('petState').value,
                        latitude: document.getElementById('petLatitude').value,
                        longitude: document.getElementById('petLongitude').value,
                    }),
                });

                writeLog(JSON.stringify(result, null, 2));
                await loadPets();
            } catch (error) {
                writeLog(`Erro ao criar pet: ${error.message}`);
            }
        }

        elements.loginButton.addEventListener('click', login);
        elements.sessionButton.addEventListener('click', loadSession);
        elements.logoutButton.addEventListener('click', logout);
        elements.createPetButton.addEventListener('click', createPet);
        elements.refreshPets.addEventListener('click', loadPets);
        elements.refreshRequests.addEventListener('click', loadRequests);

        renderSession();
        loadPets();
        loadRequests();
    </script>
</body>
</html>
HTML;
    }
}
