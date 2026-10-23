/**
 * LogTec - Substituição de Caixas de Diálogo Nativas por MODAL Customizado
 * Atende ao requisito: "sempre trate as mensagens de caixa de mensagens nativas do navegador em um MODAL"
 */

const AppModal = {
    overlay: null,
    titleEl: null,
    bodyEl: null,
    footerEl: null,
    isOpen: false,

    init() {
        if (this.overlay) return;

        // Cria a estrutura do modal caso não exista no DOM
        let modalContainer = document.getElementById('app-modal-wrapper');
        if (!modalContainer) {
            modalContainer = document.createElement('div');
            modalContainer.id = 'app-modal-wrapper';
            modalContainer.innerHTML = `
                <div id="modal-overlay" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-200">
                    <div id="modal-box" class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden border border-slate-100 transform scale-95 transition-all duration-200">
                        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                            <h3 id="modal-title" class="text-lg font-bold text-slate-800 flex items-center gap-2">
                                <span id="modal-icon"></span>
                                <span id="modal-title-text">Aviso</span>
                            </h3>
                            <button id="modal-close-x" type="button" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                        <div id="modal-body" class="p-6 text-slate-600 text-sm leading-relaxed"></div>
                        <div id="modal-footer" class="px-6 py-3.5 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2.5"></div>
                    </div>
                </div>
            `;
            document.body.appendChild(modalContainer);
        }

        this.overlay = document.getElementById('modal-overlay');
        this.titleEl = document.getElementById('modal-title-text');
        this.iconEl = document.getElementById('modal-icon');
        this.bodyEl = document.getElementById('modal-body');
        this.footerEl = document.getElementById('modal-footer');
        this.boxEl = document.getElementById('modal-box');

        document.getElementById('modal-close-x').addEventListener('click', () => this.close());

        // Fechar ao clicar fora (no overlay)
        this.overlay.addEventListener('click', (e) => {
            if (e.target === this.overlay) {
                this.close();
            }
        });

        // Fechar com tecla ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isOpen) {
                this.close();
            }
        });
    },

    open({ title = 'Aviso', body = '', footer = '', icon = '' }) {
        this.init();
        this.titleEl.textContent = title;
        this.iconEl.innerHTML = icon || '';
        this.bodyEl.innerHTML = body;
        this.footerEl.innerHTML = footer;

        this.overlay.classList.remove('opacity-0', 'pointer-events-none');
        this.overlay.classList.add('opacity-100');
        this.boxEl.classList.remove('scale-95');
        this.boxEl.classList.add('scale-100');
        this.isOpen = true;
    },

    close() {
        if (!this.overlay) return;
        this.overlay.classList.remove('opacity-100');
        this.overlay.classList.add('opacity-0', 'pointer-events-none');
        this.boxEl.classList.remove('scale-100');
        this.boxEl.classList.add('scale-95');
        this.isOpen = false;
    },

    /**
     * Substituição moderna para alert()
     */
    alert(message, title = 'Notificação LogTec') {
        return new Promise((resolve) => {
            const footer = `
                <button id="modal-btn-ok" type="button" class="w-full sm:w-auto px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl shadow-sm transition">
                    Entendido
                </button>
            `;
            const icon = `<svg class="w-5 h-5 text-blue-600 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>`;

            this.open({
                title,
                body: `<p class="text-slate-700">${this.escapeHtml(message)}</p>`,
                footer,
                icon
            });

            document.getElementById('modal-btn-ok').focus();
            document.getElementById('modal-btn-ok').onclick = () => {
                this.close();
                resolve(true);
            };
        });
    },

    /**
     * Substituição moderna para confirm()
     */
    confirm(message, title = 'Confirmação Necessária') {
        return new Promise((resolve) => {
            const footer = `
                <button id="modal-btn-cancel" type="button" class="px-4 py-2 border border-slate-200 text-slate-600 hover:bg-slate-100 font-semibold text-sm rounded-xl transition">
                    Cancelar
                </button>
                <button id="modal-btn-confirm" type="button" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl shadow-sm transition">
                    Confirmar
                </button>
            `;
            const icon = `<svg class="w-5 h-5 text-amber-500 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>`;

            this.open({
                title,
                body: `<p class="text-slate-700 font-medium">${this.escapeHtml(message)}</p>`,
                footer,
                icon
            });

            document.getElementById('modal-btn-cancel').onclick = () => {
                this.close();
                resolve(false);
            };

            document.getElementById('modal-btn-confirm').onclick = () => {
                this.close();
                resolve(true);
            };
        });
    },

    /**
     * Substituição moderna para prompt()
     */
    prompt(message, defaultValue = '', title = 'Entrada de Dados') {
        return new Promise((resolve) => {
            const inputId = 'modal-prompt-input';
            const body = `
                <label class="block text-slate-700 font-medium mb-2">${this.escapeHtml(message)}</label>
                <input id="${inputId}" type="text" value="${this.escapeHtml(defaultValue)}" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none text-slate-800 text-sm" placeholder="Digite aqui...">
            `;
            const footer = `
                <button id="modal-btn-cancel" type="button" class="px-4 py-2 border border-slate-200 text-slate-600 hover:bg-slate-100 font-semibold text-sm rounded-xl transition">
                    Cancelar
                </button>
                <button id="modal-btn-confirm" type="button" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl shadow-sm transition">
                    Salvar
                </button>
            `;

            this.open({ title, body, footer });

            const input = document.getElementById(inputId);
            input.focus();
            input.select();

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    document.getElementById('modal-btn-confirm').click();
                }
            });

            document.getElementById('modal-btn-cancel').onclick = () => {
                this.close();
                resolve(null);
            };

            document.getElementById('modal-btn-confirm').onclick = () => {
                const val = input.value;
                this.close();
                resolve(val);
            };
        });
    },

    escapeHtml(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }
};

// Sobrescreve as caixas nativas do navegador para cumprir a regra do requisitos.md:
// "sempre trate as mensagens de caixa de mensagens de caixa de mensagens nativas de mensagens nativas do navegador em um MODAL"
window.alert = function(msg) {
    AppModal.alert(msg);
};

window.confirmAsync = function(msg, title) {
    return AppModal.confirm(msg, title);
};

window.promptAsync = function(msg, defaultVal, title) {
    return AppModal.prompt(msg, defaultVal, title);
};

window.AppModal = AppModal;
document.addEventListener('DOMContentLoaded', () => AppModal.init());
