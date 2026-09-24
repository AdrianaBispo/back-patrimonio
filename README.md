# Back Patrimônio

API backend do sistema de patrimônio, construída com Laravel, autenticação JWT e permissões Spatie.

## Stack

- PHP 8.3+
- Laravel 13
- [tymon/jwt-auth](https://github.com/tymondesigns/jwt-auth) para autenticação
- [spatie/laravel-permission](https://github.com/spatie/laravel-permission) para roles/permissões
- SQLite por padrão (configurável no `.env`)

## Pré-requisitos

- PHP 8.3+
- Composer
- Extensão PDO SQLite (ou outro banco configurado no `.env`)

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

### Dados iniciais

Antes de usar o cadastro de usuários:

1. Existir ao menos um **departamento** em `departamentos` (`departamento_id` é obrigatório).
2. Existir a role **`Administrador`** (guard `api`), pois o registro de usuários exige um admin autenticado.
3. O primeiro usuário Administrador precisa ser criado manualmente (seed/tinker), pois `POST /api/auth/register` só pode ser chamado por um admin já logado.

Exemplos:

```bash
php artisan permission:create-role Administrador api
php artisan permission:create-role Operador api
```

```php
// tinker — criar departamento e admin inicial
$dept = App\Models\Departamento::create(['nome' => 'TI']);
$admin = App\Models\User::create([
    'nome' => 'Admin',
    'celular' => '11999999999',
    'departamento_id' => $dept->id,
    'email' => 'admin@email.com',
    'password' => 'senha1234', // o cast hashed do User aplica o hash
]);
$admin->assignRole('Administrador');
```

## Autenticação

As rotas de auth ficam sob o prefixo `/api/auth`.

O login devolve o JWT no JSON **e** no cookie HttpOnly `access_token`.

| Endpoint | Como autentica |
|----------|----------------|
| `POST /api/auth/register` | Cookie `access_token` |
| `GET /api/auth/user` | Cookie `access_token` |
| `PUT /api/auth/users/{id}` | Cookie `access_token` |
| `POST /api/auth/logout` | Header `Authorization: Bearer {token}` (`jwt.auth`) |
| `GET /api/roles/all` | Middleware Spatie `role:Administrador` (usuário autenticado com a role) |

> No frontend, envie credenciais/cookies (`credentials: 'include'` / `withCredentials: true`) para que o cookie `access_token` chegue no backend.

### Endpoints

| Método | Rota | Auth | Descrição |
|--------|------|------|-----------|
| `POST` | `/api/auth/login` | Não | Login; emite token + cookie |
| `POST` | `/api/auth/register` | Cookie + role `Administrador` | Criar usuário |
| `PUT` | `/api/auth/users/{id}` | Cookie | Editar usuário |
| `POST` | `/api/auth/logout` | Bearer | Invalidar token |
| `GET` | `/api/auth/user` | Cookie | Usuário autenticado |
| `GET` | `/api/roles/all` | Role `Administrador` | Listar roles (`id`, `name`) |

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
  "email": "admin@email.com",
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

Também define o cookie `access_token` (HttpOnly, 60 minutos).

**Erros comuns:**

- `401` — credenciais inválidas
- `422` — validação de campos

---

### Registrar usuário

`POST /api/auth/register`

Requer cookie `access_token` de um usuário com role **Administrador**.

**Body (JSON):**

| Campo | Tipo | Obrigatório | Regras |
|-------|------|-------------|--------|
| `nome` | string | Sim | máx. 255 |
| `celular` | string | Sim | máx. 20 |
| `departamento_id` | uuid | Sim | deve existir em `departamentos` |
| `email` | string | Sim | email válido e único |
| `password` | string | Sim | mín. 8 caracteres |
| `roles` | array | Sim | IDs de roles existentes |

```json
{
  "nome": "João Silva",
  "celular": "11999999999",
  "departamento_id": "01a09cd1-d877-7033-9209-cffdd4a6d341",
  "email": "joao@email.com",
  "password": "senha1234",
  "roles": [4]
}
```

**Resposta `201`:**

```json
{
  "message": "Usuário registrado com sucesso",
  "user": { }
}
```

**Erros comuns:**

- `401` — cookie ausente ou token inválido
- `403` — autenticado, mas sem role Administrador
- `422` — validação de campos

---

### Editar usuário

`PUT /api/auth/users/{id}`

Requer cookie `access_token`.

**Regras de autorização:**

- **Administrador**: pode editar qualquer usuário e alterar `roles`
- **Demais usuários**: só podem editar o próprio registro; o campo `roles` é ignorado

**Body (JSON):**

| Campo | Tipo | Obrigatório | Regras |
|-------|------|-------------|--------|
| `nome` | string | Sim | máx. 255 |
| `celular` | string | Sim | máx. 20 |
| `departamento_id` | uuid | Sim | deve existir em `departamentos` |
| `email` | string | Sim | email válido e único (exceto o próprio) |
| `password` | string | Não | mín. 8 caracteres; omitir para manter |
| `roles` | array | Não | IDs de roles; só aplicado se o autenticado for Administrador |

```json
{
  "nome": "João Silva Atualizado",
  "celular": "11988887777",
  "departamento_id": "01a09cd1-d877-7033-9209-cffdd4a6d341",
  "email": "joao@email.com",
  "roles": [4]
}
```

**Resposta `200`:**

```json
{
  "message": "Usuário editado com sucesso",
  "user": { }
}
```

**Erros comuns:**

- `401` — cookie ausente ou token inválido
- `403` — tentar editar outro usuário sem ser Administrador
- `404` — usuário não encontrado
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

Requer cookie `access_token`.

**Resposta `200`:**

```json
{
  "user": { }
}
```

---

### Listar roles

`GET /api/roles/all`

Requer usuário autenticado com role **Administrador**.

**Resposta `200`:**

```json
[
  { "id": 3, "name": "Administrador" },
  { "id": 4, "name": "Operador" }
]
```

## Senhas

O model `User` usa o cast `'password' => 'hashed'`. Envie a senha em texto puro no JSON; o Laravel aplica o hash automaticamente. Não use `Hash::make` no controller junto com esse cast (evita hash duplo e quebra o login).

## Estrutura principal

```
app/
  Http/Controllers/AuthControllers/   # login, register, edit, logout, user
  Http/Controllers/RoleControllers/   # listagem de roles
  Middleware/JwtMiddleware.php        # proteção JWT (alias jwt.auth)
  Models/User.php
  Models/Departamento.php
routes/
  api.php                             # rotas da API
database/migrations/                  # users, departamentos, permission tables
```

## Exemplos com cURL

```bash
# Login (grava cookie em cookies.txt)
curl -X POST http://127.0.0.1:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -c cookies.txt \
  -d "{\"email\":\"admin@email.com\",\"password\":\"senha1234\"}"

# Listar roles (Administrador)
curl http://127.0.0.1:8000/api/roles/all \
  -H "Accept: application/json" \
  -b cookies.txt

# Registrar usuário (Administrador + cookie)
curl -X POST http://127.0.0.1:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -b cookies.txt \
  -d "{\"nome\":\"João Silva\",\"celular\":\"11999999999\",\"departamento_id\":\"UUID_DO_DEPARTAMENTO\",\"email\":\"joao@email.com\",\"password\":\"senha1234\",\"roles\":[4]}"

# Editar usuário
curl -X PUT http://127.0.0.1:8000/api/auth/users/UUID_DO_USUARIO \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -b cookies.txt \
  -d "{\"nome\":\"João Atualizado\",\"celular\":\"11988887777\",\"departamento_id\":\"UUID_DO_DEPARTAMENTO\",\"email\":\"joao@email.com\"}"

# Usuário autenticado
curl http://127.0.0.1:8000/api/auth/user \
  -H "Accept: application/json" \
  -b cookies.txt

# Logout (Bearer)
curl -X POST http://127.0.0.1:8000/api/auth/logout \
  -H "Authorization: Bearer SEU_TOKEN" \
  -H "Accept: application/json"
```

## Licença

MIT
