---

# 🚀 Desafio Técnico — Vaga Back-End | VExpenses

Esta aplicação foi desenvolvida como parte do **desafio técnico** para a vaga de **Desenvolvedor Back-end** na **VExpenses**.

📁 **Recursos Úteis:**
- 📬 Collection Postman `postman_collection/Vexpenses.postman_collection.json`  
- 📄 Arquivo de teste CSV `csv_test_file/usuarios_teste.csv`

---

## 📌 Funcionalidades

### 🔐 Autenticação

- **1.1 Registro de Usuário:**  
  Registro com envio de e-mail automático para novos usuários.

- **1.2 Login com JWT:**  
  Autenticação via **JWT** com dados criptografados.

- **1.3 Refresh Token:**  
  Renovação segura do token de acesso.

- **1.4 Esqueci Minha Senha:**  
  Envio de link de redefinição via e-mail.

- **1.5 Redefinição de Senha:**  
  Requisição via link/token para criação de nova senha.

- **1.6 Logout:**  
  Finaliza a sessão autenticada.

- **1.7 Upload CSV:**  
  Upload de arquivos `.csv` com **processamento assíncrono** (jobs).  
  A **porcentagem de progresso** é armazenada no **Redis**.

- **1.8 Status de Importação:**  
  Exibe o progresso atual baseado no valor em cache.

- **1.9 Me:**  
  Exibe os dados do usuário autenticado.

- **1.10 Listagem de Usuários:**  
  Retorna usuários com paginação (10 por página).

---

### 📄 Logs da Aplicação

- **2.1 Listagem de Logs:**  
  Exibe ações realizadas por usuários.  
  Logs sem usuário autenticado (como jobs) são salvos anonimamente.

- **2.2 Visualização de Log (Show):**  
  Apresenta um log específico, com dados do usuário quando aplicável.

---

## 🏗️ Arquitetura do Projeto

### 🧵 Fila (Jobs)

- Gerenciada via **Supervisord**, com **8 workers simultâneos**.
- Execução contínua e tolerante a falhas.
- Jobs armazenados no **Redis** para alta performance e não sobrecarregar o banco.

### ⚙️ Servidor

- Servidor **FrankenPHP** com suporte a **Octane**.
- Alta escalabilidade e performance para requisições simultâneas.

### 📦 Redis

- Utilizado para:
  - Gerenciamento de filas.
  - Armazenamento de progresso de importações.
- Reduz carga no banco de dados relacional (**MySQL**).

### 🔁 Supervisord

- Responsável por:
  - Iniciar e manter workers em execução.
  - Reiniciar workers em caso de falhas.
  - Iniciar o servidor web.

---

## ▶️ Como Executar a Aplicação

Execute os comandos abaixo em seu terminal:

```bash
# Criar rede docker
docker network create vexpenses

# Build e subida dos containers
docker compose up --build
```

---

### Portas externas e hosts
 - Laravel: esta apontando para: localhost:8030
 - MySql: esta apontando para: localhost:3306
 - Redis: esta apontando para: localhost:6379