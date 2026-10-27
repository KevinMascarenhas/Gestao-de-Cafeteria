# Sistema de Gestão do Cardápio - Cafeteria

Projeto de CRUD completo em PHP procedural com MySQLi para gerenciamento de cardápio de cafeteria local.

## O que o sistema faz

Permite que o gerente da cafeteria cadastre, liste, edite e exclua itens do cardápio através de um painel administrativo protegido por login.

## Requisitos

- Servidor web com PHP (ex: XAMPP, WAMP, Laragon)
- MySQL ou MariaDB
- phpMyAdmin (opcional, para gerenciar o banco)

## Instalação

1. Copie todos os arquivos para a pasta do servidor (ex: `htdocs` do XAMPP).
2. Inicie o Apache e o MySQL no painel de controle.
3. Crie o banco de dados acessando no navegador:

   ```
   http://localhost/atividade%20extra/setup.php
   ```

   Isso criará o banco `gestao_cafeteria`, as tabelas `usuarios` e `cardapio`, e o usuário admin padrão.

4. (Opcional) Depois de criar o banco, delete o arquivo `setup.php` por segurança.

## Acesso

Acesse o sistema pelo navegador:

```
http://localhost/atividade%20extra/login.php
```

**Usuário padrão:** `admin`

**Senha padrão:** `admin123`

## Estrutura de arquivos

| Arquivo | Função |
|---------|--------|
| `conexao.php` | Conexão com o banco MySQLi (porta 3307) |
| `auth.php` | Protege páginas que exigem login |
| `login.php` | Tela de login do gerente |
| `logout.php` | Encerra sessão |
| `painel.php` | Painel principal: cadastro + listagem |
| `cadastrar_item.php` | Lógica de CREATE |
| `editar_item.php` | Formulário de edição pré-preenchido |
| `atualizar_item.php` | Lógica de UPDATE |
| `excluir_item.php` | Lógica de DELETE |
| `setup.php` | Cria banco e tabelas automaticamente |
| `banco.sql` | Script SQL para importação manual |

## Funcionalidades

- **Login:** Autenticação simples com sessão PHP
- **Create:** Formulário para adicionar nome, categoria, descrição e preço
- **Read:** Tabela listando todos os itens cadastrados
- **Update:** Página de edição com dados pré-preenchidos, atualiza sem duplicar
- **Delete:** Botão excluir na tabela, remove item pelo ID via URL

## Segurança

- Todas as consultas ao banco usam **Prepared Statements** (`mysqli_prepare`), protegendo contra SQL Injection.
- Páginas restritas verificam se o usuário está logado antes de exibir o conteúdo.
- Saídas usam `htmlspecialchars()` para prevenir XSS.

## Banco de dados

O script SQL de criação está em `banco.sql` e também é executado automaticamente pelo `setup.php`.

Tabelas criadas:

- `usuarios` (id, nome, usuario, senha)
- `cardapio` (id, nome_item, categoria, descricao, preco, disponivel)

## Observações

- O arquivo `conexao.php` está configurado para porta `3307`. Se o seu MySQL estiver na porta padrão `3306`, altere a variável `$host` em `conexao.php` e `setup.php`.
- A senha do usuário admin está salva em texto simples conforme o script fornecido na atividade. Em ambiente de produção, use `password_hash()` e `password_verify()`.