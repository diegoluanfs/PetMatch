# Status do Projeto PetMatch

Atualizado em 2026-09-19.

## Estado atual

O PetMatch possui um MVP funcional para descoberta de pets e solicitação de adoção. O fluxo principal está disponível em:

```text
catálogo público -> autenticação -> like/pass -> favoritos/matches -> solicitação -> decisão da organização
```

Branch publicada: `main`.

## Implementado

### Plataforma e infraestrutura

- PHP 8.4, Composer, PDO e PostgreSQL.
- Docker Compose com PHP CLI, PHP-FPM, Nginx e PostgreSQL.
- Migrations versionadas e executor de migrations.
- Seed idempotente para demonstração local.
- Arquitetura separada em Domain, Application, Infrastructure e Presentation.
- Repositories PDO e prepared statements.
- Sessão HTTP com cookie `petmatch_session`.

### Identidade e acesso

- Cadastro de usuário.
- Login e logout.
- Consulta da sessão atual.
- Papéis `adopter`, `organization_admin` e `admin`.
- Consulta autenticada das verificações do usuário.
- Regra de adoção exigindo e-mail e WhatsApp verificados.
- Conta de adotante demo verificada pelo seed.

### Catálogo e organizações

- Cadastro, edição e consulta de pets.
- Listagem pública somente de pets disponíveis.
- Listagem administrativa dos pets da própria organização.
- Upload e remoção de fotos.
- Arquivamento lógico de pets.
- Registros arquivados permanecem no banco para auditoria.
- Operações administrativas bloqueadas para organizações que não estejam `active`.

### Descoberta e engajamento

- Like e pass, com decisão única por usuário e pet.
- Listagem de interesses.
- Favoritos idempotentes, com criação, listagem e remoção.
- Matches definidos no MVP como pets curtidos que continuam disponíveis.
- Interface pública com catálogo, autenticação, interesses, favoritos e matches.

### Adoção

- Criação de solicitação para pet disponível.
- Apenas adotantes podem solicitar.
- Apenas usuários com e-mail e WhatsApp verificados podem solicitar.
- Listagem das solicitações do adotante.
- Cancelamento de solicitação pendente.
- Listagem administrativa para a organização responsável.
- Aprovação e rejeição pela organização responsável.
- Aprovação transacional: solicitação aprovada e pet adotado na mesma transação.
- Histórico preservado para auditoria.

### Qualidade e documentação

- PHPUnit com cobertura unitária dos principais casos de uso.
- PHPStan sem erros no nível configurado.
- Manual técnico da API em [api.md](api.md).
- README com setup, seed, credenciais e URLs.
- Dashboard `/playground` para validação manual da organização.

## Ainda falta

### Prioridade alta

1. **Testes de integração e feature**
   - Testar endpoints HTTP com PostgreSQL isolado.
   - Validar cookies de sessão e respostas HTTP reais.
   - Cobrir o fluxo completo de cadastro até adoção.
   - Evitar que os testes usem o banco de desenvolvimento local.

2. **Verificações reais de contato**
   - Implementar solicitação e confirmação de e-mail.
   - Implementar confirmação de WhatsApp.
   - Definir se a verificação documental entra no MVP ou em uma fase posterior.
   - O seed atual é apenas uma solução de demonstração local.

3. **Segurança de produção**
   - Proteção CSRF nos endpoints autenticados por sessão.
   - Rate limiting para login, cadastro e endpoints sensíveis.
   - Política de expiração e renovação de sessão.
   - Hardening adicional de upload e armazenamento.

### Prioridade média

4. **Gestão completa de organizações**
   - Aprovar organização.
   - Suspender organização.
   - Editar dados da organização.
   - Interface administrativa para essas transições.

5. **Feed estilo Tinder**
   - Exibir um pet por vez.
   - Navegação entre pets.
   - Filtros de descoberta.
   - Evitar exibir novamente pets já avaliados.

6. **Experiência de adoção**
   - Mostrar o nome do pet nas solicitações, em vez de somente o ID.
   - Exibir informações da organização.
   - Melhorar mensagens de bloqueio por falta de verificação.
   - Notificações sobre aprovação, rejeição e mudanças de status.

7. **PHP CS Fixer no container**
   - O PHP CS Fixer está configurado, mas o processo não encerra corretamente no ambiente Docker após a análise.
   - PHPStan e PHPUnit estão funcionando normalmente.

### Pós-MVP

- Upload e revisão de identidade/documentos.
- Marca d'água de fotos de organizações.
- Moderação avançada de pets e imagens.
- Chat entre adotante e organização.
- Notificações por e-mail, WhatsApp ou push.
- Busca geográfica e recomendações.
- Relatórios e auditoria administrativa avançada.

## Critério sugerido para o próximo marco

O próximo marco pode ser considerado concluído quando houver:

- testes HTTP com banco PostgreSQL isolado;
- fluxo completo de adoção coberto automaticamente;
- CSRF e rate limiting implementados;
- confirmação real de e-mail e WhatsApp, ou decisão documentada de adiar essas integrações;
- documentação da API mantida junto das alterações de contrato.
