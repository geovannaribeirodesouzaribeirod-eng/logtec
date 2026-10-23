/**
 * LogTec - Sistema de Notificações TOAST
 * Atende ao requisito: "Retorne sempre as mensagens de erros de forma claras na interface para o usuário (TOAST)"
 */

const Toast = {
    container: null,

    init() {
        if (!this.container) {
            this.container = document.getElementById('toast-container');
            if (!this.container) {
                this.container = document.createElement('div');
                this.container.id = 'toast-container';
                this.container.className = 'fixed top-4 right-4 z-50 flex flex-col space-y-3 max-w-sm w-full pointer-events-none px-4 sm:px-0';
                document.body.appendChild(this.container);
            }
        }
    },

    show({ type = 'info', title = '', message = '', duration = 4500 }) {
        this.init();

        const toast = document.createElement('div');
        toast.className = `pointer-events-auto flex items-start p-4 rounded-xl shadow-xl border toast-enter transition-all duration-300 text-slate-800 ${this.getThemeClasses(type)}`;

        const iconSvg = this.getIcon(type);

        toast.innerHTML = `
            <div class="flex-shrink-0 mr-3 mt-0.5">
                ${iconSvg}
            </div>
            <div class="flex-1 text-sm">
                ${title ? `<h4 class="font-bold text-slate-900 mb-0.5">${this.escapeHtml(title)}</h4>` : ''}
                <p class="leading-relaxed font-medium">${this.escapeHtml(message)}</p>
            </div>
            <button type="button" class="ml-3 text-slate-400 hover:text-slate-700 transition focus:outline-none close-toast" aria-label="Fechar">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        `;

        const closeBtn = toast.querySelector('.close-toast');
        const dismiss = () => {
            toast.classList.remove('toast-enter');
            toast.classList.add('toast-exit');
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 300);
        };

        closeBtn.addEventListener('click', dismiss);

        if (duration > 0) {
            setTimeout(dismiss, duration);
        }

        this.container.appendChild(toast);
    },

    success(message, title = 'Sucesso!') {
        this.show({ type: 'success', title, message });
    },

    error(message, title = 'Erro!') {
        this.show({ type: 'error', title, message, duration: 6000 });
    },

    warning(message, title = 'Atenção!') {
        this.show({ type: 'warning', title, message });
    },

    info(message, title = 'Informação') {
        this.show({ type: 'info', title, message });
    },

    getThemeClasses(type) {
        switch (type) {
            case 'success':
                return 'bg-emerald-50 border-emerald-200 text-emerald-950 shadow-emerald-900/10';
            case 'error':
                return 'bg-rose-50 border-rose-200 text-rose-950 shadow-rose-900/10';
            case 'warning':
                return 'bg-amber-50 border-amber-200 text-amber-950 shadow-amber-900/10';
            case 'info':
            default:
                return 'bg-sky-50 border-sky-200 text-sky-950 shadow-sky-900/10';
        }
    },

    getIcon(type) {
        switch (type) {
            case 'success':
                return '<svg class="w-5 h-5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>';
            case 'error':
                return '<svg class="w-5 h-5 text-rose-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>';
            case 'warning':
                return '<svg class="w-5 h-5 text-amber-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>';
            case 'info':
            default:
                return '<svg class="w-5 h-5 text-sky-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>';
        }
    },

    escapeHtml(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }
};

window.Toast = Toast;
document.addEventListener('DOMContentLoaded', () => Toast.init());
