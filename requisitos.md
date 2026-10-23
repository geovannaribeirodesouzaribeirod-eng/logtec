# Log Tec

## OBJETIVOS


### STACK TECNÓLOGICO
- Backend: PHP estruturado em sessões nativas
- Banco de dados: MySQL (PDO para segurança)
- Frontend: HTML5, PHP, CSS, Tailwind CSS

#### Regras de negocio (CORE)
- usuário se cadastra no sistema.
  um único usuário por cpf
- o sistema terá vários templates de listas, ex: listas de mercado, listas de musicas, de filmes viagens, cada uma com um ícone diferente que o usuário pode alterar.
- terá duas maneiras de entrar, como empresa (entrando com cnpj), e como funcionário a procura de emprego
-  

##### Regras globais
- Use sempre o mesmo PDO para conexão e quieres no MySQL para evitar SQL Injections
- Mantenha o código limpo e comente apenas logicas complexas.
- Separe os arquivos  de forma logica: um arquivo para conexão com a base (bd.php) e scrips de backend isolados em views em HTML5/PHP
- Estilize as telas em tailwind de forma responsiva priorizando o MobileFrist.
- Retorne sempre as mensagens de erros de forma claras na interface para o usuário (TOAST)
- sempre trate as mensagens de caixa de mensagens de caixa de mensagens nativas de mensagens nativas do navegador em um MODAL

