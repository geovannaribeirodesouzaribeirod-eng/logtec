/**
 * LogTec - Script Principal de Interação e Regras de Negócio no Frontend
 *
 * Funcionalidades:
 * - Máscaras dinâmicas de CPF, CNPJ, Telefone
 * - Validação em tempo real de Maioridade (+18 anos) no formulário
 * - Alternância intuitiva entre login/cadastro de Empresa (CNPJ) e Funcionário (CPF)
 * - Rastreamento e Simulação de Rotas em Tempo Real com Leaflet
 * - Confirmação via Modal para exclusões e ações críticas
 */

// Formatação e Máscaras
const InputMasks = {
    cpf(value) {
        return value
            .replace(/\D/g, '')
            .slice(0, 11)
            .replace(/(\d{3})(\d)/, '$1.$2')
            .replace(/(\d{3})(\d)/, '$1.$2')
            .replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    },

    cnpj(value) {
        return value
            .replace(/\D/g, '')
            .slice(0, 14)
            .replace(/(\d{2})(\d)/, '$1.$2')
            .replace(/(\d{3})(\d)/, '$1.$2')
            .replace(/(\d{3})(\d)/, '$1/$2')
            .replace(/(\d{4})(\d{1,2})$/, '$1-$2');
    },

    telefone(value) {
        const clean = value.replace(/\D/g, '').slice(0, 11);
        if (clean.length > 10) {
            return clean.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
        }
        return clean.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3');
    },

    init() {
        document.querySelectorAll('input[data-mask="cpf"]').forEach(input => {
            input.addEventListener('input', (e) => {
                e.target.value = InputMasks.cpf(e.target.value);
            });
        });

        document.querySelectorAll('input[data-mask="cnpj"]').forEach(input => {
            input.addEventListener('input', (e) => {
                e.target.value = InputMasks.cnpj(e.target.value);
            });
        });

        document.querySelectorAll('input[data-mask="telefone"]').forEach(input => {
            input.addEventListener('input', (e) => {
                e.target.value = InputMasks.telefone(e.target.value);
            });
        });
    }
};

// Validador de Maioridade no Frontend
function verificarMaioridadeInput(inputData) {
    if (!inputData || !inputData.value) return true;

    const dataNasc = new Date(inputData.value + 'T00:00:00');
    if (isNaN(dataNasc.getTime())) return true;

    const hoje = new Date();
    let idade = hoje.getFullYear() - dataNasc.getFullYear();
    const m = hoje.getMonth() - dataNasc.getMonth();
    if (m < 0 || (m === 0 && hoje.getDate() < dataNasc.getDate())) {
        idade--;
    }

    if (idade < 18) {
        Toast.warning('Você precisa ter pelo menos 18 anos de idade para se cadastrar.', 'Maioridade Obrigatória');
        inputData.classList.add('border-rose-500', 'bg-rose-50');
        return false;
    } else {
        inputData.classList.remove('border-rose-500', 'bg-rose-50');
        return true;
    }
}

// Menu Mobile Toggle
function initMobileMenu() {
    const btn = document.getElementById('btn-mobile-menu');
    const menu = document.getElementById('mobile-menu');
    if (btn && menu) {
        btn.addEventListener('click', () => {
            menu.classList.toggle('hidden');
        });
    }
}

// Interceptor de Exclusão com Modal (Substitui confirm nativo)
function initConfirmModals() {
    document.querySelectorAll('[data-confirm]').forEach(el => {
        el.addEventListener('click', async (e) => {
            e.preventDefault();
            const message = el.getAttribute('data-confirm') || 'Tem certeza que deseja prosseguir com esta ação?';
            const confirmed = await AppModal.confirm(message, 'Confirmação');
            if (confirmed) {
                if (el.tagName === 'A') {
                    window.location.href = el.href;
                } else if (el.type === 'submit' && el.form) {
                    el.form.submit();
                }
            }
        });
    });
}

// Inicialização Global do DOM
document.addEventListener('DOMContentLoaded', () => {
    InputMasks.init();
    initMobileMenu();
    initConfirmModals();

    const nascInput = document.getElementById('data_nascimento');
    if (nascInput) {
        nascInput.addEventListener('change', () => verificarMaioridadeInput(nascInput));
    }
});
