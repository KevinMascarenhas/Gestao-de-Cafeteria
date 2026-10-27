# Sistema de Gestao do Cardapio - Cafeteria

Projeto de CRUD completo em PHP procedural com MySQLi para gerenciamento de cardapio de cafeteria local.

## O que o sistema faz

Permite que o gerente da cafeteria cadastre, liste, edite e exclua itens do cardapio através de um painel administrativo protegido por login.

## Requisitos

- Servidor web com PHP (ex: XAMPP, WAMP, Laragon)
- MySQL ou MariaDB
- phpMyAdmin (opcional, para gerenciar o banco)

## Instalacao

1. Copie todos os arquivos para a pasta do servidor (ex: `htdocs` do XAMPP).
2. Inicie o Apache e o MySQL no painel de controle.
3. Crie o banco de dados acessando no navegador:

   ```
   http://localhost/atividade%20extra/setup.php
   ```

   Isso criará o banco `gestao_cafeteria`, as tabelas `usuarios` e `cardapio`, e o usuario admin padrão.

4. (Opcional) Depois de criar o banco, delete o arquivo `setup.php` por seguranca.

## Acesso

Acesse o sistema pelo navegador:

```
http://localhost/atividade%20extra/login.php
```

**Usuario padrao:** `admin`  
**Senha padrao:** `admin123`

## Estrutura de arquivos

| Arquivo | Funcao |
|---------|--------|
| `conexao.php` | Conexao com o banco MySQLi (porta 3307) |
| `auth.php` | Protege paginas que exigem login |
| `login.php` | Tela de login do gerente |
| `logout.php` | Encerra sessao |
| `painel.php` | Painel principal: cadastro + listagem |
| `cadastrar_item.php` | Logica de CREATE |
| `editar_item.php` | Formulario de edicao pre-preenchido |
| `atualizar_item.php` | Logica de UPDATE |
| `excluir_item.php` | Logica de DELETE |
| `setup.php` | Cria banco e tabelas automaticamente |
| `banco.sql` | Script SQL para importacao manual |

## Funcionalidades

- **Login:** Autenticacao simples com sessao PHP
- **Create:** Formulario para adicionar nome, categoria, descricao e preco
- **Read:** Tabela listando todos os itens cadastrados
- **Update:** Pagina de edicao com dados pre-preenchidos, atualiza sem duplicar
- **Delete:** Botao excluir na tabela, remove item pelo ID via URL

## Seguranca

- Todas as consultas ao banco usam **Prepared Statements** (`mysqli_prepare`), protegendo contra SQL Injection.
- Paginas restritas verificam se o usuario esta logado antes de exibir o conteúdo.
- Saidas usam `htmlspecialchars()` para prevenir XSS.

## Banco de dados

O script SQL de criacao esta em `banco.sql` e também e executado automaticamente pelo `setup.php`.

Tabelas criadas:

- `usuarios` (id, nome, usuario, senha)
- `cardapio` (id, nome_item, categoria, descricao, preco, disponivel)

## Observacoes

- O arquivo `conexao.php` esta configurado para porta `3307`. Se o seu MySQL estiver na porta padrão `3306`, altere a variável `$host` em `conexao.php` e `setup.php`.
- A senha do usuario admin esta salva em texto simples conforme o script fornecido na atividade. Em ambiente de producao, use `password_hash()` e `password_verify()`.