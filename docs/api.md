# PetMatch API

Manual técnico para integrar clientes web, mobile ou serviços externos ao PetMatch.

## Ambiente local

```text
Base URL: http://localhost:8080
API URL:  http://localhost:8080/api/v1
```

A API usa JSON e autenticação por sessão HTTP. O cliente deve preservar o cookie `petmatch_session` entre login e as próximas requisições.

## Formato das respostas

Sucesso:

```json
{
  "data": {}
}
```

Erro:

```json
{
  "error": "Mensagem do erro"
}
```

## Autenticação

### Criar conta

`POST /api/v1/auth/register`

```json
{
  "name": "Maria",
  "email": "maria@example.com",
  "password": "secret123"
}
```

Cria um usuário `adopter` com status inicial `pending`. A resposta não cria sessão automaticamente.

### Login

`POST /api/v1/auth/login`

```json
{
  "email": "maria@example.com",
  "password": "secret123"
}
```

Exemplo com `curl`:

```bash
curl -i -c cookies.txt \
  -H "Content-Type: application/json" \
  -d '{"email":"maria@example.com","password":"secret123"}' \
  http://localhost:8080/api/v1/auth/login
```

Use `-b cookies.txt` nas requisições autenticadas.

### Sessão atual

`GET /api/v1/auth/me`

### Verificações do usuário

`GET /api/v1/auth/verifications`

Retorna somente tipo, status e data de verificação. Para solicitar adoção, o usuário precisa ter `email` e `whatsapp` com status `verified`.

### Logout

`POST /api/v1/auth/logout`

## Catálogo público

### Listar pets disponíveis

`GET /api/v1/pets`

Não exige autenticação. Retorna apenas pets com status `available`.

### Detalhar pet

`GET /api/v1/pets/{id}`

## Interações do adotante

Todas exigem sessão autenticada.

### Registrar like ou pass

`POST /api/v1/pets/{id}/swipe`

```json
{
  "action": "like"
}
```

Valores possíveis: `like` e `pass`. A decisão é única por usuário/pet e pode ser atualizada.

### Listar interesses

`GET /api/v1/me/liked-pets`

### Listar matches

`GET /api/v1/me/matches`

No MVP, match significa um pet curtido que ainda está disponível. Não há reciprocidade entre usuários.

### Criar favorito

`POST /api/v1/pets/{id}/favorite`

O favorito é idempotente.

### Listar favoritos

`GET /api/v1/me/favorites`

### Remover favorito

`DELETE /api/v1/pets/{id}/favorite`

## Solicitações de adoção

### Criar solicitação

`POST /api/v1/adoption-requests`

Exige usuário `adopter`, e-mail verificado, WhatsApp verificado e pet disponível.

```json
{
  "pet_id": 12,
  "message": "Tenho um quintal seguro e experiência com cães."
}
```

### Listar solicitações do adotante

`GET /api/v1/adoption-requests`

### Cancelar solicitação pendente

`PATCH /api/v1/adoption-requests/{id}/withdraw`

Somente o dono da solicitação pode cancelar e apenas no status `pending`.

## Operações da organização

Exigem sessão de `organization_admin` ou `admin`. A organização precisa estar `active` para operações administrativas.

### Criar organização

`POST /api/v1/organizations`

```json
{
  "name": "Amigos de Quatro Patas",
  "description": "Organização de resgate e adoção.",
  "email": "contato@example.com",
  "phone": "5555999990000"
}
```

### Listar pets da organização

`GET /api/v1/organizations/pets`

Inclui pets disponíveis, adotados e arquivados da própria organização.

### Criar pet

`POST /api/v1/pets`

```json
{
  "name": "Thor",
  "description": "Labrador dócil",
  "animal_type": "dog",
  "breed": "labrador",
  "gender": "male",
  "birth_date": "2022-01-01",
  "size": "large",
  "city": "Santa Maria",
  "state": "RS"
}
```

### Atualizar pet

`PUT /api/v1/pets/{id}`

Somente pets disponíveis e pertencentes à organização atual podem ser atualizados.

### Remover pet do catálogo

`PATCH /api/v1/pets/{id}/archive`

Esta é uma exclusão lógica. O registro permanece no banco com status `archived` para auditoria.

### Adicionar foto

`POST /api/v1/pets/{id}/photos`

Aceita JSON com caminho ou `multipart/form-data` com arquivo. Exemplo:

```bash
curl -i -b cookies.txt \
  -F "photo=@thor.jpg" \
  -F "sort_order=0" \
  http://localhost:8080/api/v1/pets/12/photos
```

### Remover foto

`DELETE /api/v1/pets/{id}/photos/{photoId}`

### Solicitações recebidas

`GET /api/v1/organizations/adoption-requests`

### Aprovar solicitação

`PATCH /api/v1/adoption-requests/{id}/approve`

A operação é transacional: a solicitação vira `approved` e o pet vira `adopted` juntos.

### Rejeitar solicitação

`PATCH /api/v1/adoption-requests/{id}/reject`

## Códigos HTTP principais

- `200`: operação concluída
- `201`: recurso criado
- `401`: sessão ausente ou inválida
- `403`: usuário autenticado sem permissão ou verificação exigida
- `404`: recurso inexistente
- `409`: conflito, como solicitação ativa duplicada
- `422`: dados inválidos ou pet indisponível

## Contas locais de demonstração

Criadas por `docker compose run --rm app php bin/seed.php`:

- Organização: `ong.admin@example.com` / `secret123`
- Adotante verificado: `maria@example.com` / `secret123`
