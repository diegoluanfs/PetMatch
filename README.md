# PetMatch

Base inicial do projeto de adoção de pets em PHP moderno.

## Etapa atual

MVP funcional: catálogo público, autenticação, likes/pass, interesses, solicitações de adoção e gestão pela organização.

## Estrutura

- `public/`: ponto de entrada da aplicação
- `src/`: código-fonte da aplicação
- `database/`: migrations e seeders
- `tests/`: testes unitários, de integração e de feature
- `docs/`: decisões de arquitetura e evolução do projeto

## Próximo passo

Criar a primeira migration, a conexão PDO e o bootstrap mínimo da aplicação.

## Banco de dados local com Docker

Suba a aplicação PHP, o Nginx e o PostgreSQL com:

```bash
docker compose up -d
```

Para testar a interface mínima, acesse:

```bash
http://localhost:8080/health
```

Depois instale as dependências e execute as migrations de dentro do container da aplicação CLI:

```bash
docker compose run --rm app composer install
docker compose run --rm app php bin/migrate.php
docker compose run --rm app php bin/verify-schema.php
```

Para criar dados locais reproduzíveis para demonstração:

```bash
docker compose run --rm app php bin/seed.php
```

Credenciais locais:

- Organização: `ong.admin@example.com` / `secret123`
- Adotante: `maria@example.com` / `secret123`

Interfaces:

- Catálogo público: `http://localhost:8080/`
- Área de testes da organização: `http://localhost:8080/playground`
- Manual técnico da API: [`docs/api.md`](docs/api.md)

Execute a suíte automatizada com:

```bash
composer test
```
