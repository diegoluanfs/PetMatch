# PetMatch

Base inicial do projeto de adoção de pets em PHP moderno.

## Etapa atual

Etapa 1: arquitetura, domínio e banco.

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
