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
| `DELETE /api/auth/users/{id}` | Cookie `access_token` |
| `POST /api/auth/logout` | Cookie `access_token` |
| `POST /api/permissions/create` | Cookie `access_token` |
| `GET /api/roles/all` | Cookie `access_token` |
| `PUT /api/roles/update/{id}` | Cookie `access_token` |
| `/api/equipaments/*` | Cookie `access_token` + permissão Spatie |
| `/api/historic_equipaments/*` | Cookie `access_token` + permissão Spatie |

> No frontend, envie credenciais/cookies (`credentials: 'include'` / `withCredentials: true`) para que o cookie `access_token` chegue no backend.

### Endpoints

| Método | Rota | Auth | Descrição |
|--------|------|------|-----------|
| `POST` | `/api/auth/login` | Não | Login; emite token + cookie |
| `POST` | `/api/auth/register` | Cookie + role `Administrador` | Criar usuário |
| `PUT` | `/api/auth/users/{id}` | Cookie | Editar usuário |
| `DELETE` | `/api/auth/users/{id}` | Cookie | Inativar usuário |
| `POST` | `/api/auth/logout` | Cookie | Invalidar token |
| `GET` | `/api/auth/user` | Cookie | Usuário autenticado |
| `POST` | `/api/permissions/create` | Cookie | Criar permissão |
| `GET` | `/api/roles/all` | Cookie | Listar roles (`id`, `name`) |
| `PUT` | `/api/roles/update/{id}` | Cookie | Sincronizar permissões da role |
| `GET` | `/api/equipaments/all` | Cookie + `get-all-equipaments` | Listar equipamentos |
| `GET` | `/api/equipaments/get` | Cookie + `get-equipaments` | Buscar equipamentos |
| `POST` | `/api/equipaments/create` | Cookie + `create-equipaments` | Criar equipamento |
| `PUT` | `/api/equipaments/update/{id}` | Cookie + `update-equipaments` | Atualizar equipamento |
| `GET` | `/api/historic_equipaments/all` | Cookie + `get-all-historic-equipaments` | Listar histórico |
| `GET` | `/api/historic_equipaments/get` | Cookie + `get-historic-equipaments` | Filtrar histórico |
| `POST` | `/api/historic_equipaments/create` | Cookie + `create-historic-equipaments` | Registrar histórico |

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

### Inativar usuário

`DELETE /api/auth/users/{id}`

Requer cookie `access_token`.

**Regras de autorização:**

- **Administrador**: pode inativar qualquer usuário
- **Demais usuários**: só podem inativar o próprio registro

O campo `status` do usuário passa a `inativo`.

**Resposta `200`:**

```json
{
  "message": "Usuário inativado com sucesso"
}
```

**Erros comuns:**

- `401` — cookie ausente ou token inválido
- `403` — tentar inativar outro usuário sem ser Administrador
- `404` — usuário não encontrado

---

### Logout

`POST /api/auth/logout`

Requer cookie `access_token`.

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

Requer cookie `access_token`.

**Resposta `200`:**

```json
[
  { "id": 3, "name": "Administrador" },
  { "id": 4, "name": "Operador" }
]
```

---

### Criar permissão

`POST /api/permissions/create`

Requer cookie `access_token`. Usuários com a role `users` recebem `403`.

**Body (JSON):**

| Campo | Tipo | Obrigatório | Regras |
|-------|------|-------------|--------|
| `name` | string | Sim | máx. 255, único em `permissions` |

```json
{
  "name": "get-all-equipaments"
}
```

**Resposta `201`:**

```json
{
  "message": "Permissão criada com sucesso",
  "permission": { }
}
```

**Erros comuns:**

- `401` — cookie ausente ou token inválido
- `403` — role `users`
- `422` — validação de campos

Permissões usadas pelas rotas de patrimônio:

| Permissão | Rota |
|-----------|------|
| `get-all-equipaments` | `GET /api/equipaments/all` |
| `get-equipaments` | `GET /api/equipaments/get` |
| `create-equipaments` | `POST /api/equipaments/create` |
| `update-equipaments` | `PUT /api/equipaments/update/{id}` |
| `get-all-historic-equipaments` | `GET /api/historic_equipaments/all` |
| `get-historic-equipaments` | `GET /api/historic_equipaments/get` |
| `create-historic-equipaments` | `POST /api/historic_equipaments/create` |

---

### Atualizar permissões da role

`PUT /api/roles/update/{id}`

Requer cookie `access_token`. Substitui o conjunto de permissões da role (`sync`).

**Body (JSON):**

| Campo | Tipo | Obrigatório | Regras |
|-------|------|-------------|--------|
| `role_id` | uuid | Sim | deve existir em `roles` |
| `permissions` | array | Sim | IDs existentes em `permissions` |

```json
{
  "role_id": "uuid-da-role",
  "permissions": ["uuid-permissao-1", "uuid-permissao-2"]
}
```

**Resposta `200`:**

```json
{
  "message": "Permissões da role atualizadas com sucesso."
}
```

**Erros comuns:**

- `401` — cookie ausente ou token inválido
- `404` — role não encontrada
- `422` — validação de campos
- `500` — falha ao sincronizar

---

## Equipamentos

Todas as rotas exigem cookie `access_token` e a permissão indicada na tabela de endpoints.

### Listar equipamentos

`GET /api/equipaments/all`

Permissão: `get-all-equipaments`.

**Query:**

| Parâmetro | Tipo | Obrigatório | Descrição |
|-----------|------|-------------|-----------|
| `per_page` | int | Não | Se maior que 0, devolve paginação do Laravel; caso contrário, a lista completa |

A resposta inclui `status_nome` e `usuario_nome` (joins com `status` e `users`), ordenada por `nome`.

**Resposta `200` (sem paginação):**

```json
{
  "equipamentos": []
}
```

---

### Buscar equipamentos

`GET /api/equipaments/get`

Permissão: `get-equipaments`.

**Query:**

| Parâmetro | Tipo | Obrigatório | Descrição |
|-----------|------|-------------|-----------|
| `name` | string | Não | Filtro `like` no nome |
| `serie` | string | Não | Filtro `like` na série |
| `per_page` | int | Não | Paginação quando maior que 0 |

Os filtros são aplicados de forma exclusiva: `name`, depois `serie`. Sem filtro, devolve todos os equipamentos.

---

### Criar equipamento

`POST /api/equipaments/create`

Permissão: `create-equipaments`.

**Body (JSON):**

| Campo | Tipo | Obrigatório | Regras |
|-------|------|-------------|--------|
| `nome` | string | Sim | máx. 255 |
| `descricao` | string | Sim | máx. 255 |
| `serie` | string | Sim | máx. 255, apenas dígitos |
| `status_id` | int | Sim | deve existir em `status` |
| `imagem_url` | string | Sim | máx. 255 |
| `usuario_id` | uuid | Sim | deve existir em `users` |

```json
{
  "nome": "Notebook Dell",
  "descricao": "Notebook da equipe de TI",
  "serie": "123456",
  "status_id": 1,
  "imagem_url": "https://exemplo.com/notebook.png",
  "usuario_id": "uuid-do-usuario"
}
```

**Resposta `201`:**

```json
{
  "message": "Equipamento criado com sucesso",
  "equipamento": { }
}
```

**Erros comuns:**

- `400` — status ou usuário inexistente
- `401` / `403` — cookie ausente ou sem a permissão
- `422` — validação de campos
- `500` — falha ao persistir

---

### Atualizar equipamento

`PUT /api/equipaments/update/{id}`

Permissão: `update-equipaments`.

O body segue as mesmas regras de `POST /api/equipaments/create`.

**Resposta `200`:**

```json
{
  "message": "Equipamento atualizado com sucesso"
}
```

**Erros comuns:**

- `400` — status ou usuário inexistente
- `404` — equipamento não encontrado
- `422` — validação de campos
- `500` — falha ao atualizar

---

## Histórico de equipamentos

Todas as rotas exigem cookie `access_token` e a permissão correspondente.

### Listar histórico

`GET /api/historic_equipaments/all`

Permissão: `get-all-historic-equipaments`.

**Query:**

| Parâmetro | Tipo | Obrigatório | Descrição |
|-----------|------|-------------|-----------|
| `per_page` | int | Não | Se maior que 0, devolve paginação; caso contrário, a lista completa |

Cada item inclui `equipament_nome`, `status_nome` e `user_nome`. Ordenação por `date_time`.

**Resposta `200` (sem paginação):**

```json
{
  "historic_equipaments": []
}
```

---

### Filtrar histórico

`GET /api/historic_equipaments/get`

Permissão: `get-historic-equipaments`.

**Query:**

| Parâmetro | Tipo | Obrigatório | Descrição |
|-----------|------|-------------|-----------|
| `historic` | uuid | Não | ID exato do registro de histórico |
| `equipament_name` | string | Não | `like` no nome do equipamento |
| `equipament_serie` | string | Não | `like` na série do equipamento |
| `user_name` | string | Não | `like` no nome do usuário |
| `per_page` | int | Não | Paginação quando maior que 0 |

Os filtros são aplicados de forma exclusiva, nesta ordem: `historic`, `equipament_name`, `equipament_serie`, `user_name`.

---

### Criar histórico

`POST /api/historic_equipaments/create`

Permissão: `create-historic-equipaments`.

**Body (JSON):**

| Campo | Tipo | Obrigatório | Regras |
|-------|------|-------------|--------|
| `equipament_id` | id | Sim | deve existir em `equipaments` |
| `status_id` | id | Sim | deve existir em `status` |
| `user_id` | id | Sim | deve existir em `users` |

```json
{
  "equipament_id": "uuid-do-equipamento",
  "status_id": 1,
  "user_id": "uuid-do-usuario"
}
```

**Resposta `201`:**

```json
{
  "message": "Histórico do equipamento criado com sucesso",
  "historic_equipament": { }
}
```

**Erros comuns:**

- `401` / `403` — cookie ausente ou sem a permissão
- `422` — equipamento, status ou usuário inexistente
- `500` — falha ao criar o histórico

## Senhas

O model `User` usa o cast `'password' => 'hashed'`. Envie a senha em texto puro no JSON; o Laravel aplica o hash automaticamente. Não use `Hash::make` no controller junto com esse cast (evita hash duplo e quebra o login).

## Estrutura principal

```
app/
  Http/Controllers/AuthControllers/        # login, register, edit, logout, user
  Http/Controllers/UserControllers/        # edição e inativação de usuário
  Http/Controllers/RoleControllers/        # listagem e permissões da role
  Http/Controllers/Permissions/            # criação de permissões
  Http/Controllers/EquipamentoControllers/ # CRUD de equipamentos
  Http/Controllers/HistoricEquipaments/    # histórico de equipamentos
  Middleware/JwtMiddleware.php             # proteção JWT (alias jwt.auth)
  Models/User.php
  Models/Departamento.php
routes/
  api.php                                  # rotas da API
database/migrations/                       # users, departamentos, equipamentos, histórico, permissions
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

# Inativar usuário
curl -X DELETE http://127.0.0.1:8000/api/auth/users/UUID_DO_USUARIO \
  -H "Accept: application/json" \
  -b cookies.txt

# Criar equipamento
curl -X POST http://127.0.0.1:8000/api/equipaments/create \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -b cookies.txt \
  -d "{\"nome\":\"Notebook Dell\",\"descricao\":\"Notebook da equipe de TI\",\"serie\":\"123456\",\"status_id\":1,\"imagem_url\":\"https://exemplo.com/notebook.png\",\"usuario_id\":\"UUID_DO_USUARIO\"}"

# Listar equipamentos
curl "http://127.0.0.1:8000/api/equipaments/all?per_page=15" \
  -H "Accept: application/json" \
  -b cookies.txt

# Registrar histórico
curl -X POST http://127.0.0.1:8000/api/historic_equipaments/create \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -b cookies.txt \
  -d "{\"equipament_id\":\"UUID_DO_EQUIPAMENTO\",\"status_id\":1,\"user_id\":\"UUID_DO_USUARIO\"}"

# Listar histórico
curl "http://127.0.0.1:8000/api/historic_equipaments/all" \
  -H "Accept: application/json" \
  -b cookies.txt

# Logout (cookie)
curl -X POST http://127.0.0.1:8000/api/auth/logout \
  -H "Accept: application/json" \
  -b cookies.txt
```

## Licença

MIT
