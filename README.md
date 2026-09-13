# Back Patrimônio

API backend do sistema de patrimônio, construída com Laravel e autenticação JWT.

## Stack

- PHP 8.3+
- Laravel 13
- [tymon/jwt-auth](https://github.com/tymondesigns/jwt-auth) para autenticação

## Pré-requisitos

- PHP 8.3+
- Composer
- Banco de dados configurado no `.env` (MySQL/PostgreSQL/SQLite)

## Instalação

```bash
composer install
cp .env.example .env   # se ainda não existir .env
php artisan key:generate
php artisan jwt:secret
php artisan migrate
php artisan serve
```

A API fica disponível em `http://127.0.0.1:8000`.

> Antes de registrar usuários, é necessário existir ao menos um **departamento** na tabela `departamentos` (o campo `departamento_id` é obrigatório).

## Autenticação

As rotas de auth ficam sob o prefixo `/api/auth`.

Rotas protegidas exigem o header:

```http
Authorization: Bearer {access_token}
```

### Endpoints

| Método | Rota | Auth | Descrição |
|--------|------|------|-----------|
| `POST` | `/api/auth/register` | Não | Criar usuário |
| `POST` | `/api/auth/login` | Não | Login e emissão de token |
| `POST` | `/api/auth/logout` | Sim | Invalidar token |
| `GET` | `/api/auth/user` | Sim | Usuário autenticado |

---

### Registrar usuário

`POST /api/auth/register`

**Body (JSON):**

| Campo | Tipo | Obrigatório | Regras |
|-------|------|-------------|--------|
| `nome` | string | Sim | máx. 255 |
| `celular` | string | Sim | máx. 20 |
| `departamento_id` | uuid | Sim | deve existir em `departamentos` |
| `email` | string | Sim | email válido e único |
| `password` | string | Sim | mín. 8 caracteres |

```json
{
  "nome": "João Silva",
  "celular": "11999999999",
  "departamento_id": "01a09cd1-d877-7033-9209-cffdd4a6d341",
  "email": "joao@email.com",
  "password": "senha1234"
}
```

**Resposta `201`:**

```json
{
  "message": "Usuário registrado com sucesso"
}
```

---

### Login

`POST /api/auth/login`

**Body (JSON):**

| Campo | Tipo | Obrigatório | Regras |
|-------|------|-------------|--------|
| `email` | string | Sim | email válido |
| `password` | string | Sim | mín. 8 caracteres |

```json
{
  "email": "joao@email.com",
  "password": "senha1234"
}
```

**Resposta `200`:**

```json
{
  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
  "token_type": "bearer"
}
```

**Erros comuns:**

- `401` — credenciais inválidas
- `422` — validação de campos

---

### Logout

`POST /api/auth/logout`

Header: `Authorization: Bearer {token}`

**Resposta `200`:**

```json
{
  "message": "Logout successful"
}
```

---

### Usuário autenticado

`GET /api/auth/user`

Header: `Authorization: Bearer {token}`

Retorna os dados do usuário logado.

## Estrutura principal

```
app/
  Http/Controllers/AuthControllers/   # login, register, logout, user
  Middleware/JwtMiddleware.php        # proteção JWT (alias jwt.auth)
  Models/User.php
  Models/Departamento.php
routes/
  api.php                             # rotas da API
database/migrations/                  # users, departamentos, etc.
```

## Exemplos com cURL

```bash
# Registro
curl -X POST http://127.0.0.1:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d "{\"nome\":\"João Silva\",\"celular\":\"11999999999\",\"departamento_id\":\"UUID_DO_DEPARTAMENTO\",\"email\":\"joao@email.com\",\"password\":\"senha1234\"}"

# Login
curl -X POST http://127.0.0.1:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d "{\"email\":\"joao@email.com\",\"password\":\"senha1234\"}"

# Usuário autenticado
curl http://127.0.0.1:8000/api/auth/user \
  -H "Authorization: Bearer SEU_TOKEN" \
  -H "Accept: application/json"

# Logout
curl -X POST http://127.0.0.1:8000/api/auth/logout \
  -H "Authorization: Bearer SEU_TOKEN" \
  -H "Accept: application/json"
```

## Licença

MIT
