<!-- LogTec - Estrutura Centralizada de Modal (Substitui Dialogs Nativos) -->
<div id="app-modal-wrapper">
    <div id="modal-overlay" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-200">
        <div id="modal-box" class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden border border-slate-100 transform scale-95 transition-all duration-200">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 id="modal-title" class="text-lg font-bold text-slate-800 flex items-center gap-2">
                    <span id="modal-icon"></span>
                    <span id="modal-title-text">Aviso</span>
                </h3>
                <button id="modal-close-x" type="button" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition" aria-label="Fechar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div id="modal-body" class="p-6 text-slate-600 text-sm leading-relaxed"></div>
            <div id="modal-footer" class="px-6 py-3.5 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2.5"></div>
        </div>
    </div>
</div>
