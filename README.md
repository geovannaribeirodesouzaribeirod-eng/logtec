# LogTec - Sistema de Gestão Logística e Entregas em Tempo Real

> Sistema desenvolvido com base estrita no documento [`requisitos.md`](file:///c:/Users/Aluno/Desktop/logtec/requisitos.md).

## 🚀 Sobre o Projeto
O **LogTec** é uma solução completa para gestão de entregas expressas, rotas em tempo real e intermediação entre **Empresas** e **Funcionários/Entregadores à procura de emprego**.

---

## 🎯 Objetivos Implementados
1. **Facilitação de entrada para ambas as partes**:
   - Acesso simplificado para Empresas (via CNPJ).
   - Acesso para Funcionários / Entregadores à procura de emprego (via CPF único e validação de maioridade).
2. **Entregas em tempo real**:
   - Telemetria e rastreamento com atualização dinâmica de status (`pendente` &rarr; `em_transito` &rarr; `entregue`).
3. **Rotas de entrega**:
   - Mapa interativo com Leaflet.js / OpenStreetMap, cálculo de trajeto e simulação de deslocamento ao vivo do entregador.
4. **Pedidos pendentes e entregas**:
   - Fila de pedidos pendentes para rápida aceitação por motoristas parceiros e acompanhamento pelas empresas.

---

## 🛠️ Stack Tecnológico
- **Backend:** PHP estruturado em sessões nativas (`$_SESSION`).
- **Banco de Dados:** MySQL com PDO único (Singleton) e Prepared Statements contra SQL Injection. Suporte adicional a SQLite transparente para testes locais sem configuração prévia.
- **Frontend:** HTML5, PHP, CSS3, Tailwind CSS (MobileFirst) e Leaflet.js.
- **Componentes:**
  - Sistema de Notificações flutuantes **TOAST** (sucesso, erro, alerta, info).
  - Sistema de **MODAL** substituindo caixas nativas do navegador (`alert`, `confirm`, `prompt`).

---

## 📋 Regras de Negócio do CORE
- [x] **Cadastro com CPF Único:** O sistema valida o cálculo matemático do CPF e impede cadastros duplicados.
- [x] **Maioridade Obrigatória (+18 anos):** Validação tanto no formulário (frontend) quanto no processador backend com cálculo da data de nascimento (&ge; 18 anos).
- [x] **Duas maneiras de entrar:**
  - **Empresa:** Acesso com CNPJ e gestão de despacho de pedidos e contratação.
  - **Funcionário:** Acesso com CPF à procura de vagas e rotas de entrega.
- [x] **Templates de Listas com Ícone Alterável:**
  - Templates prontos: Mercado 🛒, Músicas 🎵, Filmes 🎬, Viagens ✈️, Operacional 📦.
  - O usuário pode criar novas listas, adicionar itens, marcar como concluído e **alterar o ícone** livremente através de um seletor visual em modal.

---

## 📂 Estrutura de Arquivos
```text
logtec/
├── index.php             # Página inicial com apresentação dos objetivos e chamada para ação
├── index.html            # Versão interativa instantânea para teste em qualquer navegador
├── login.php             # Tela de login com abas (Empresa / Funcionário)
├── cadastro.php          # Cadastro com validação de maioridade (+18) e CPF único
├── logout.php            # Destruição segura da sessão e redirecionamento
├── dashboard.php         # Painel administrativo dinâmico (Empresa ou Entregador)
├── rotas.php             # Monitoramento de rotas e entregas em tempo real com mapa GPS
├── listas.php            # Templates de listas (Mercado, Músicas, Filmes, Viagens)
├── vagas.php             # Mural de vagas de emprego e candidaturas
├── bd.php                # Conexão PDO segura (Singleton, Prepared Statements)
├── auth.php              # Sessões nativas, validação de CPF, CNPJ, maioridade e toasts
├── schema.sql            # Script SQL do banco MySQL com dados iniciais
├── requisitos.md         # Documento de requisitos original
├── actions/
│   ├── auth_action.php   # Backend: login e cadastro
│   ├── pedidos_action.php# Backend: criação, aceitação e finalização de pedidos
│   ├── listas_action.php # Backend: criação de listas, troca de ícone e itens
│   └── vagas_action.php  # Backend: postagem de vagas e candidaturas
├── includes/
│   ├── header.php        # Cabeçalho responsivo e navegação
│   ├── footer.php        # Rodapé e carregamento de scripts
│   ├── toast.php         # Componente de notificação Toast
│   └── modal.php         # Componente de caixa de diálogo Modal
└── assets/
    ├── css/custom.css    # Estilos customizados e animações
    └── js/
        ├── app.js        # Máscaras de CPF/CNPJ/Tel e validações
        ├── toast.js      # Gerenciador de Toast
        └── modal.js      # Substituição de alert() / confirm() por Modal
```

---

## 🔑 Credenciais de Teste Pré-cadastradas

### 1. Entregador / Funcionário (CPF):
- **CPF:** `123.456.789-00`
- **Senha:** `123456`

### 2. Empresa (CNPJ):
- **CNPJ:** `12.345.678/0001-99`
- **Senha:** `123456`

---

## ⚙️ Como Executar

### Opção 1: Servidor PHP Embutido (XAMPP, Laragon ou PHP CLI)
1. Certifique-se de ter o PHP instalado no seu ambiente.
2. Na pasta do projeto, execute:
   ```bash
   php -S localhost:8000
   ```
3. Acesse `http://localhost:8000` no seu navegador.
4. Para conectar ao MySQL:
   - Importe o arquivo `schema.sql` no phpMyAdmin ou cliente MySQL.
   - O arquivo `bd.php` se conecta automaticamente ao MySQL `localhost`, usuário `root`, senha vazia (ou variáveis de ambiente).

### Opção 2: Visualização Direta no Navegador
- Basta abrir o arquivo `index.html` em qualquer navegador (Google Chrome, Edge, Firefox). Ele inclui um simulador reativo completo de todas as telas, rotas e templates de listas.

---

## 🌐 Deploy em Hospedagem (InfinityFree)
Compatível com a hospedagem gratuita InfinityFree (`https://logtec.infinityfree.io/`):
- Envie todos os arquivos para a pasta `htdocs/`.
- Crie o banco de dados MySQL no painel vPanel e importe `schema.sql`.
- Configure as credenciais no `bd.php` ou via variáveis de ambiente.
