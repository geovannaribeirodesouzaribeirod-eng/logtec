    </main>

    <!-- Rodapé Responsivo & Tecnologias -->
    <footer class="bg-white border-t border-slate-200 mt-16 text-slate-500 text-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                
                <!-- Coluna 1: LogTec -->
                <div class="md:col-span-2">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center text-white font-bold text-sm">
                            <i class="fa-solid fa-truck-fast"></i>
                        </div>
                        <span class="text-lg font-bold text-slate-900 tracking-tight">LogTec</span>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-500 max-w-sm mb-4 leading-relaxed">
                        Plataforma inteligente de entregas em tempo real, gestão de rotas e conexão direta entre empresas e entregadores parceiros.
                    </p>
                    <div class="flex items-center gap-2 text-xs text-slate-400">
                        <span class="px-2 py-0.5 rounded bg-slate-100 font-mono">PHP + PDO</span>
                        <span class="px-2 py-0.5 rounded bg-slate-100 font-mono">MySQL</span>
                        <span class="px-2 py-0.5 rounded bg-slate-100 font-mono">Tailwind CSS</span>
                        <span class="px-2 py-0.5 rounded bg-slate-100 font-mono">MobileFirst</span>
                    </div>
                </div>

                <!-- Coluna 2: Funcionalidades -->
                <div>
                    <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-3">Soluções</h4>
                    <ul class="space-y-2 text-xs sm:text-sm">
                        <li><a href="rotas.php" class="hover:text-blue-600 transition">Entregas em Tempo Real</a></li>
                        <li><a href="rotas.php" class="hover:text-blue-600 transition">Otimização de Rotas</a></li>
                        <li><a href="dashboard.php" class="hover:text-blue-600 transition">Pedidos Pendentes</a></li>
                        <li><a href="listas.php" class="hover:text-blue-600 transition">Templates de Listas</a></li>
                    </ul>
                </div>

                <!-- Coluna 3: Acessos -->
                <div>
                    <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-3">Acessos Rápidos</h4>
                    <ul class="space-y-2 text-xs sm:text-sm">
                        <li><a href="login.php?tipo=empresa" class="hover:text-blue-600 transition">Entrar como Empresa (CNPJ)</a></li>
                        <li><a href="login.php?tipo=funcionario" class="hover:text-blue-600 transition">Entrar como Funcionário (CPF)</a></li>
                        <li><a href="cadastro.php" class="hover:text-blue-600 transition">Cadastrar Nova Conta</a></li>
                        <li><a href="vagas.php" class="hover:text-blue-600 transition">Vagas de Emprego</a></li>
                    </ul>
                </div>

            </div>

            <div class="border-t border-slate-100 pt-6 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-400 gap-4">
                <p>&copy; <?= date('Y') ?> LogTec. Todos os direitos reservados.</p>
                <p class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span>
                    Sistema Operacional Online & Protegido com PDO
                </p>
            </div>
        </div>
    </footer>

    <!-- Leaflet.js Scripts para Mapas e Rotas -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    
    <!-- Scripts do Sistema LogTec -->
    <script src="assets/js/toast.js"></script>
    <script src="assets/js/modal.js"></script>
    <script src="assets/js/app.js"></script>

</body>
</html>
