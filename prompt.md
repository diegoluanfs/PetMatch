Quero desenvolver uma aplicação web de adoção de pets chamada **PetMatch**, utilizando **PHP moderno e PDO** para acesso ao banco de dados.

Quero que você atue como um **arquiteto de software e desenvolvedor PHP sênior**, com experiência em aplicações web, APIs REST, Clean Architecture, SOLID, DDD, segurança e testes automatizados.

O objetivo principal deste projeto é construir uma aplicação **realista, profissional, segura e bem estruturada**, que possa ser utilizada como projeto de portfólio para demonstrar minhas habilidades em PHP.

## 1. Conceito da aplicação

O PetMatch será uma plataforma para conectar pessoas interessadas em adotar animais com pets disponíveis para adoção.

A experiência principal será inspirada no Tinder:

* o usuário visualiza um pet por vez;
* pode deslizar para a esquerda/direita ou utilizar botões;
* "curtir" demonstra interesse;
* "passar" ignora o animal;
* pets curtidos podem aparecer posteriormente;
* quando houver interesse, o usuário poderá iniciar uma solicitação de adoção;
* organizações/ONGs poderão cadastrar e gerenciar seus animais.

O projeto deve começar como um **MVP**, mas sua arquitetura precisa permitir evolução futura.

## 1.1 Como você deve trabalhar

Você é responsável por propor e implementar soluções tecnicamente justificadas, mas não deve inventar requisitos silenciosamente.

Antes de cada alteração:

1. identifique o comportamento que será implementado ou corrigido;
2. leia os arquivos e testes diretamente relacionados;
3. declare brevemente a hipótese técnica e a abordagem escolhida;
4. faça a menor alteração coerente com a arquitetura existente.

Se faltar uma informação que altere significativamente o domínio, o contrato da API ou a segurança, faça uma pergunta objetiva antes de codificar. Para decisões de baixo risco, escolha a alternativa mais simples, registre a suposição e continue.

Não reescreva arquivos ou módulos não relacionados. Preserve alterações existentes no workspace, não faça commit e não crie branches sem solicitação explícita.

Ao encontrar um erro, reproduza-o com o menor comando ou teste possível, corrija a causa raiz e execute novamente a mesma verificação antes de ampliar o escopo.

## 1.2 Fluxo público e autenticação

A lista de pets disponíveis deve ser acessível sem autenticação. Visitantes podem visualizar os pets, seus detalhes e fotos, mas não podem executar ações de interação.

As ações de like, dislike e solicitação de adoção exigem uma sessão autenticada. Quando um visitante tentar executar uma dessas ações, a interface deve encaminhá-lo para login ou cadastro e preservar o destino original para que ele retorne ao fluxo após autenticar.

Após o login ou cadastro, usuários adotantes podem interagir com os pets e acompanhar seus interesses e solicitações. Usuários de organizações acessam as ferramentas de gestão dos pets e das solicitações pertencentes à própria organização.

O cadastro público deve criar um usuário adotante com status inicial `pending`. A interface pública não deve expor ferramentas administrativas de organizações para visitantes ou adotantes.

---

# 2. Stack obrigatória

Utilize:

* PHP 8.4+;
* Composer;
* PDO;
* PostgreSQL;
* HTML5;
* CSS3;
* JavaScript moderno;
* Docker;
* PHPUnit ou Pest para testes;
* PHPStan para análise estática;
* PHP CS Fixer ou ferramenta equivalente para padronização.

### IMPORTANTE SOBRE BANCO DE DADOS

**NÃO utilize ORM.**

Não utilize:

* Eloquent;
* Doctrine ORM;
* Propel;
* RedBean;
* Active Record;
* qualquer outro ORM.

O acesso ao banco deverá ser feito exclusivamente através de:

```php
PDO
```

Utilize:

* prepared statements;
* parâmetros nomeados;
* transações;
* tratamento adequado de exceções;
* repositories;
* migrations SQL ou uma solução simples baseada em PDO.

Quero aprender e demonstrar conhecimento de **PDO e SQL**, portanto as consultas SQL devem ser explícitas.

---

# 3. Arquitetura

Não quero um projeto procedural nem um MVC onde controllers, SQL e regras de negócio ficam misturados.

Utilize uma arquitetura inspirada em:

* Clean Architecture;
* SOLID;
* DDD tático;
* Separation of Concerns;
* Dependency Injection;
* Repository Pattern;
* Service/Use Case Pattern;
* DTOs;
* Value Objects quando fizerem sentido.

Porém:

**não aplique padrões apenas por aplicar.**

A arquitetura deve ser proporcional ao tamanho do projeto.

Evite:

* overengineering;
* abstrações desnecessárias;
* dezenas de interfaces sem necessidade;
* factories inúteis;
* design patterns artificiais;
* microserviços.

O projeto deve continuar simples de entender e manter.

---

# 4. Estrutura desejada

Proponha uma estrutura semelhante a:

```text
petmatch/
│
├── public/
│   └── index.php
│
├── src/
│   ├── Domain/
│   │   ├── User/
│   │   ├── Pet/
│   │   ├── Organization/
│   │   ├── Swipe/
│   │   ├── Match/
│   │   └── Adoption/
│   │
│   ├── Application/
│   │   ├── User/
│   │   ├── Pet/
│   │   ├── Swipe/
│   │   ├── Match/
│   │   └── Adoption/
│   │
│   ├── Infrastructure/
│   │   ├── Database/
│   │   ├── Persistence/
│   │   ├── Storage/
│   │   └── Http/
│   │
│   └── Presentation/
│       ├── Controllers/
│       ├── Requests/
│       └── Responses/
│
├── database/
│   ├── migrations/
│   └── seeders/
│
├── tests/
│   ├── Unit/
│   ├── Integration/
│   └── Feature/
│
├── config/
│
├── routes/
│
├── storage/
│
├── docker/
│
├── composer.json
├── docker-compose.yml
├── phpunit.xml
├── phpstan.neon
└── README.md
```

Você pode modificar essa estrutura se houver uma justificativa técnica melhor.

---

# 5. Banco de dados

Antes de escrever código, modele o banco.

Inicialmente considere entidades como:

### users

```text
id
name
email
password_hash
role
status
created_at
updated_at
```

O perfil do usuário deve distinguir claramente os níveis de confiança, sem usar um único campo booleano `verified`:

* cadastro realizado;
* e-mail validado;
* número de WhatsApp validado;
* identidade/documento enviado e aprovado por uma revisão.

Modele essas verificações de forma auditável, preferencialmente com uma entidade `user_verifications` ou equivalente:

```text
id
user_id
type                 -- email, whatsapp ou identity_document
status               -- pending, verified, rejected, expired
value_hash           -- quando for necessário evitar duplicidade sem expor o valor
provider_reference   -- opcional, para serviços externos
verified_at
reviewed_by          -- usuário administrador, quando aplicável
rejection_reason     -- nunca exponha esse campo sem autorização
created_at
updated_at
```

Não armazene documento de identidade em texto puro. Se o MVP precisar armazenar o arquivo, use armazenamento privado, nome interno aleatório, validação de tipo e tamanho, controle de acesso, criptografia quando apropriado e política de retenção. O número completo do documento não deve aparecer em logs, respostas da API ou telas públicas.

Os estados exibidos no perfil devem ser derivados dos registros de verificação e não aceitos do cliente. Se forem mantidos campos resumidos em `users` por performance, defina como serão sincronizados e qual é a fonte de verdade.

### organizations

```text
id
name
description
email
phone
created_at
updated_at
```

### pets

```text
id
organization_id
name
description
animal_type
breed
gender
birth_date
size
status
city
state
latitude
longitude
created_at
updated_at
```

### pet_photos

```text
id
pet_id
path
sort_order
created_at
```

### swipes

```text
id
user_id
pet_id
action
created_at
```

### favorites

```text
id
user_id
pet_id
created_at
```

### matches

```text
id
user_id
pet_id
created_at
```

### adoption_requests

```text
id
user_id
pet_id
status
message
created_at
updated_at
```

Se a revisão documental precisar de mais contexto ou de múltiplos documentos, avalie uma entidade separada `identity_documents`, relacionada ao usuário e com histórico de análise. Não crie essa entidade apenas por antecipação: justifique-a pelo fluxo real do MVP.

Não aceite esse modelo cegamente.

Analise:

* normalização;
* chaves estrangeiras;
* índices;
* constraints;
* UNIQUE;
* CHECK;
* integridade referencial;
* possibilidade de concorrência;
* performance.

Explique qualquer alteração proposta.

Regras adicionais para o modelo:

* use UUID ou bigint apenas após justificar a escolha; mantenha a estratégia consistente;
* defina `NOT NULL`, valores padrão e limites de tamanho sempre que o domínio permitir;
* use `timestamptz` e armazene datas em UTC;
* defina `ON DELETE` explicitamente para cada chave estrangeira;
* preserve o histórico de swipes, favoritos e solicitações de adoção;
* use constraints no banco para invariantes que também precisam ser protegidas sob concorrência;
* crie índices baseados nas consultas reais do MVP, sem indexar colunas indiscriminadamente;
* cada migration deve ser versionada, executável em banco vazio e acompanhada de rollback quando a ferramenta adotada suportar isso.

Ao propor o esquema, diferencie claramente: decisão de domínio, garantia feita pelo banco e validação feita pela aplicação.

---

# 6. Regras importantes do domínio

Defina regras de negócio claras.

Exemplos:

* somente organizações autorizadas podem cadastrar pets;
* o cadastro básico permite criar a conta, mas não concede automaticamente confiança ou autorização;
* ações que envolvam risco devem declarar o nível mínimo de verificação exigido;
* validar o e-mail e o WhatsApp são verificações independentes e não substituem a verificação documental;
* somente usuários com identidade/documento aprovado podem solicitar adoção, salvo decisão explícita diferente para o MVP;
* somente usuários com e-mail e WhatsApp validados podem iniciar uma solicitação de adoção;
* um pet adotado não pode receber novos swipes;
* um usuário não pode realizar múltiplos swipes ativos para o mesmo pet;
* uma solicitação de adoção deve possuir um status válido;
* somente usuários verificados podem solicitar adoção;
* uma organização só pode gerenciar seus próprios pets;
* um pet pode possuir várias fotos;
* remover um pet do catálogo deve ser uma exclusão lógica: altere o status para `archived` e preserve o registro e seu histórico no banco para auditoria;
* excluir um pet não deve quebrar histórico de adoção;
* alterações importantes devem respeitar transações.

Não coloque essas regras apenas nos controllers.

As regras devem pertencer ao domínio ou à camada de aplicação adequada.

### Definições do MVP

* Um `swipe` representa a decisão de um usuário sobre um pet e deve ser único por usuário e pet; se a decisão puder ser alterada, atualize o registro em vez de criar duplicatas.
* Um `favorite` representa uma intenção persistente e deve ser idempotente.
* Um `match` no MVP significa que o usuário curtiu um pet que ainda está disponível; não existe reciprocidade entre dois usuários.
* Uma solicitação de adoção possui uma máquina de estados explícita, por exemplo `pending`, `approved`, `rejected` e `withdrawn`, com transições autorizadas.
* Apenas uma solicitação ativa por usuário e pet pode existir. A organização responsável pelo pet é quem aprova ou rejeita a solicitação.
* Pets adotados, arquivados ou indisponíveis não aparecem no feed nem aceitam novas ações.
* O usuário pode estar cadastrado sem qualquer verificação, com apenas uma verificação concluída ou com todas as verificações exigidas para determinada ação.

Defina uma matriz de autorização por caso de uso. No mínimo, diferencie `registered`, `email_verified`, `whatsapp_verified` e `identity_verified`; não trate esses estados como papéis de acesso. O administrador pode aprovar ou rejeitar documentos, mas não pode alterar retroativamente o fato de que uma verificação foi solicitada ou concluída sem deixar auditoria.

Toda transição de estado deve validar o estado atual, a autorização do ator e a disponibilidade do pet dentro da mesma operação transacional quando houver risco de concorrência.

---

# 7. PDO

Crie uma implementação centralizada de conexão usando PDO.

Exemplo conceitual:

```php
final class DatabaseConnection
{
    public function create(): PDO
    {
        // ...
    }
}
```

Configure:

```php
PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
PDO::ATTR_EMULATE_PREPARES => false
```

Nunca concatene dados fornecidos pelo usuário diretamente em SQL.

Errado:

```php
$sql = "SELECT * FROM users WHERE email = '$email'";
```

Correto:

```php
$sql = "SELECT *
        FROM users
        WHERE email = :email";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    'email' => $email
]);
```

Utilize transações quando uma operação envolver múltiplas alterações relacionadas.

---

# 8. Repository Pattern

Crie repositories para abstrair persistência.

Por exemplo:

```php
interface PetRepository
{
    public function findById(int $id): ?Pet;

    public function save(Pet $pet): void;

    public function update(Pet $pet): void;

    public function delete(int $id): void;

    public function findAvailablePets(
        PetFilter $filter
    ): array;
}
```

A implementação deve utilizar PDO:

```text
PetRepository
      ↑
      │
PdoPetRepository
      │
      ▼
     PDO
      │
      ▼
 PostgreSQL
```

O domínio não deve conhecer PDO.

---

# 9. Casos de uso

Modele a aplicação através de casos de uso.

Inicialmente:

```text
RegisterUser
AuthenticateUser
VerifyEmail
VerifyWhatsapp
SubmitIdentityDocument
ReviewIdentityDocument

CreateOrganization

CreatePet
UpdatePet
GetPet
ListPets

SwipePet
ListFavorites
CreateFavorite

GetMatches

CreateAdoptionRequest
ListAdoptionRequests
ApproveAdoptionRequest
RejectAdoptionRequest

MarkPetAsAdopted
```

Cada caso de uso deve ter responsabilidade clara.

Evite controllers gigantes.

---

# 10. API

Crie uma API REST versionada:

```text
/api/v1/
```

Exemplos:

```http
POST /api/v1/auth/register
POST /api/v1/auth/login

GET /api/v1/pets
GET /api/v1/pets/{id}

POST /api/v1/pets

POST /api/v1/pets/{id}/swipe

GET /api/v1/favorites

POST /api/v1/pets/{id}/favorite

GET /api/v1/matches

POST /api/v1/adoption-requests

GET /api/v1/adoption-requests

PATCH /api/v1/adoption-requests/{id}/approve

PATCH /api/v1/adoption-requests/{id}/reject
```

Utilize corretamente:

* HTTP status codes;
* validação;
* JSON;
* tratamento de erros;
* paginação;
* filtros;
* autenticação;
* autorização.

Para cada endpoint implementado, documente método, rota, autenticação exigida, autorização, parâmetros, corpo, resposta de sucesso, erros possíveis e exemplo JSON. Use uma convenção única para erros, por exemplo `application/problem+json`, e não exponha stack traces ou detalhes de conexão em produção.

Na primeira versão, a aplicação pode servir páginas web e API no mesmo projeto. A autenticação deve usar sessão segura para a interface web; se endpoints consumidos por JavaScript exigirem autenticação, use a mesma sessão com proteção CSRF. Só adote tokens quando houver uma necessidade concreta de clientes independentes.

---

# 11. Autenticação

Implemente autenticação segura.

Não armazene senhas em texto puro.

Utilize:

```php
password_hash()
password_verify()
```

Considere autenticação baseada em sessão para a primeira versão web.

Se posteriormente a aplicação for separada em frontend/API, podemos evoluir para tokens.

Não implemente JWT apenas porque parece moderno.

Escolha a solução mais adequada ao contexto.

Defina os papéis e suas permissões de forma explícita. No mínimo, diferencie usuário adotante, organização e administrador. Nunca confie em `role`, `user_id` ou `organization_id` vindos do cliente; derive o ator da sessão autenticada e valide autorização no caso de uso.

---

# 12. Segurança

Quero que segurança seja uma parte importante do projeto.

Considere:

* SQL Injection;
* XSS;
* CSRF;
* Session Fixation;
* brute force;
* rate limiting;
* upload malicioso;
* validação de MIME type;
* validação de tamanho de arquivos;
* controle de autorização;
* exposição de informações sensíveis;
* gerenciamento de secrets;
* headers de segurança;
* cookies seguros;
* HTTPS;
* logs.

Explique cada mecanismo implementado.

---

# 13. Tinder-like UI

A interface deverá apresentar um pet por vez.

Exemplo:

```text
┌─────────────────────────┐
│                         │
│                         │
│        FOTO PET         │
│                         │
│                         │
├─────────────────────────┤
│ Thor                    │
│ Labrador • 2 anos       │
│ Santa Maria - RS        │
│                         │
│ 🐾 Brincalhão           │
│ 🏠 Adaptado a casa      │
└─────────────────────────┘

       ❌           ❤️
      PASSAR      CURTIR
```

Pode implementar inicialmente com JavaScript puro.

O frontend deve consumir a API.

Não quero que a regra de negócio seja implementada no JavaScript.

---

# 14. Sistema de recomendação

Depois do MVP, crie um serviço:

```text
PetRecommendationService
```

que possa considerar:

* distância;
* espécie;
* porte;
* idade;
* sexo;
* preferências do usuário;
* disponibilidade;
* características do animal.

Inicialmente utilize regras determinísticas.

Não utilize IA nessa primeira implementação.

A arquitetura deve permitir adicionar IA posteriormente sem reescrever o sistema.

---

# 15. IA futuramente

Depois que o sistema estiver funcionando, quero adicionar um assistente de adoção.

Exemplo:

Usuário:

> "Moro em apartamento, trabalho o dia inteiro e quero um cachorro mais tranquilo."

O sistema poderia transformar isso em preferências estruturadas:

```json
{
    "animal_type": "dog",
    "energy_level": "low",
    "environment": "apartment",
    "availability": "low"
}
```

Essas preferências poderiam alimentar o sistema de recomendação.

A IA deve ser tratada como um componente externo e opcional.

Não acople o domínio diretamente a OpenAI ou outro fornecedor.

---

# 16. Testes

Quero testes desde o desenvolvimento.

Crie:

### Unit Tests

Para:

* entidades;
* value objects;
* regras de negócio;
* services;
* use cases.

### Integration Tests

Para:

* repositories;
* PDO;
* PostgreSQL;
* transações.

### Feature Tests

Para:

* autenticação;
* criação de pets;
* swipe;
* favoritos;
* adoção.

O código deve ser desenvolvido pensando em testabilidade.

Cada caso de uso novo deve ter pelo menos um cenário de sucesso e cenários para as principais violações de regra. Inclua testes de autorização, idempotência, transições inválidas e concorrência quando aplicável. Testes de integração devem usar um PostgreSQL isolado e dados controlados, nunca o banco de desenvolvimento do usuário.

Critérios mínimos de qualidade antes de concluir uma etapa:

* testes relevantes passando;
* PHPStan sem novos erros no nível configurado;
* formatador sem alterações pendentes nos arquivos tocados;
* migrations aplicadas em banco vazio;
* documentação de execução atualizada;
* nenhuma credencial ou segredo versionado.

---

# 17. Docker

Prepare ambiente:

```text
PHP
PostgreSQL
Nginx
```

Opcionalmente:

```text
Redis
```

Não adicione Redis antes de existir uma necessidade real.

O projeto deve funcionar com:

```bash
docker compose up -d
```

---

# 18. Qualidade de código

Siga:

* PSR-4;
* PSR-12;
* SOLID;
* nomes expressivos;
* funções pequenas;
* baixo acoplamento;
* alta coesão;
* dependency injection;
* tratamento explícito de erros;
* type declarations;
* return types;
* readonly quando apropriado;
* enums quando fizer sentido.

Evite:

```php
mixed
array
```

quando uma estrutura mais explícita puder ser utilizada.

Utilize DTOs quando ajudarem a representar contratos.

---

# 19. Desenvolvimento incremental

Não gere todo o sistema de uma vez.

Quero que trabalhemos em etapas.

### Etapa 1

Arquitetura + domínio + banco.

### Etapa 2

Bootstrap PHP + Composer + configuração + PDO.

### Etapa 3

Migrations + repositories.

### Etapa 4

Usuários + autenticação.

### Etapa 5

Organizações + pets.

### Etapa 6

Swipe + favoritos + matches.

### Etapa 7

Solicitação de adoção.

### Etapa 8

Interface Tinder-like.

### Etapa 9

Testes.

### Etapa 10

Docker + CI/CD + documentação.

### Etapa 11

Sistema de recomendação.

### Etapa 12

IA.

As etapas 1 a 9 formam o MVP. Redis, recomendação avançada, notificações, chat, pagamentos e IA ficam fora do MVP até que exista uma necessidade demonstrada. Não implemente funcionalidades futuras como placeholders que aumentem a complexidade atual.

---

# 20. Regra fundamental para nossa interação

**Não avance automaticamente para a próxima etapa.**

Ao terminar cada etapa:

1. explique o que foi feito;
2. mostre a estrutura de arquivos;
3. explique as decisões arquiteturais;
4. mostre os códigos completos dos arquivos criados/modificados;
5. explique como executar;
6. forneça comandos para testar;
7. aponte possíveis melhorias;
8. aguarde minha confirmação antes de avançar.

Não mostre arquivos completos que não foram alterados nem repita blocos longos sem necessidade. Para cada etapa, entregue primeiro um resumo das mudanças, os caminhos dos arquivos, os comandos executados e os resultados da validação; mostre código completo apenas quando eu pedir ou quando o arquivo for pequeno e isso facilitar a revisão.

Se eu apresentar um erro, analise primeiro a causa e me ajude a corrigir **sem reestruturar todo o projeto desnecessariamente**.

Quando houver mais de uma solução possível, apresente a recomendada e explique brevemente as alternativas.

---

# 21. Regra contra overengineering

Este é um projeto de portfólio que precisa ser concluído.

Portanto:

> Prefira a solução mais simples que preserve boas práticas, segurança, testabilidade e capacidade de evolução.

Não quero criar um framework próprio.

Não quero implementar microsserviços.

Não quero adicionar tecnologias apenas para aumentar a quantidade de tecnologias no currículo.

Quero demonstrar que sei **tomar decisões arquiteturais adequadas ao problema**.

---

# 22. Primeiro objetivo

Comece **somente pela Etapa 1**.

Antes de escrever qualquer código, entregue:

1. visão geral da arquitetura;
2. bounded contexts/domínios identificados;
3. entidades;
4. value objects necessários;
5. enums;
6. casos de uso;
7. regras de negócio;
8. relacionamentos;
9. modelo inicial do banco;
10. diagrama ER em Mermaid;
11. estrutura inicial de diretórios;
12. justificativa das principais decisões.

**Não implemente ainda a aplicação.**

Primeiro quero revisar a arquitetura e o modelo de domínio.
