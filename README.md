---

# 🚀 Desafio Técnico - Vaga Back-End VExpenses

Esta aplicação foi desenvolvida como parte de um **desafio técnico** para a vaga de **Desenvolvedor Back-end** na **VExpenses**.

---

## 📌 Funcionalidades

### 🔐 1. Autenticação Back-end

- **1.1 Registro de Usuário**  
  Registro com envio de e-mail automático para novos usuários.

- **1.2 Login com JWT**  
  Autenticação utilizando **JWT (JSON Web Token)** com dados criptografados.

- **1.3 Refresh Token**  
  Renovação segura do token de acesso.

- **1.4 Esqueci Minha Senha**  
  Envio de link de redefinição via e-mail.

- **1.5 Redefinição de Senha**  
  Validação via token com link para criação de nova senha.

- **1.6 Logout**  
  Encerramento seguro da sessão autenticada.

- **1.7 Upload de Arquivo CSV**  
  Envio de arquivos `.csv` com **processamento assíncrono** via jobs em background. O progresso é armazenado em **cache (Redis)** com cálculo percentual.

- **1.8 Status de Importação**  
  Apresenta o progresso do upload em tempo real, baseado no cache.

- **1.9 Perfil do Usuário ("Me")**  
  Retorna os dados do usuário autenticado.

- **1.10 Listagem de Usuários**  
  Paginação com 10 usuários por página.

---

### 📄 2. Logs da Aplicação

- **2.1 Listagem de Logs**  
  Exibe logs das ações realizadas por usuários. Para jobs assíncronos, o log é salvo sem usuário.

- **2.2 Visualização de Log**  
  Exibe os detalhes de um log específico, incluindo dados do usuário, quando aplicável.

---

## 🏗️ Arquitetura do Projeto

### 🧵 1. Filas (Jobs)

- Gerenciadas com **Supervisord** (8 workers simultâneos).
- Execução contínua, tolerante a falhas.
- **Redis** como backend para performance e não sobrecarregar o banco de dados.
- Multiprocessamento para alta performance.

### ⚙️ 2. Servidor

- Utiliza o **FrankenPHP**, com suporte a **Octane**.
- Ideal para alta demanda e requisições concorrentes.

### 📦 3. Redis

- Usado para:
  - Gerenciar filas.
  - Armazenar progresso de importações.
- Alta performance de leitura/escrita sem sobrecarregar o **MySQL**.

### 🔁 4. Supervisord

- Controla:
  - Execução contínua dos workers.
  - Reinício automático em caso de falhas.
  - Inicialização do servidor.

---

## ▶️ Execução da Aplicação

Execute os comandos abaixo:

```bash
# Criar rede docker
docker network create vexpenses

# Subir os containers
docker compose up --build
```

---
