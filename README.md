# Sistema de Gestao do Cardapio - Cafeteria

CRUD completo em PHP procedural com MySQL para gerenciar itens do cardápio.

## Requisitos

- XAMPP (ou similar com PHP + MySQL)
- MySQL na porta 3307 (ajustado no `conexao.php` e `setup.php`)

## Como rodar

1. Copie esta pasta para `C:\xampp\htdocs`
2. Inicie o Apache e o MySQL no XAMPP Control Panel
3. Acesse no navegador para criar o banco:

   ```
   http://localhost/<pasta-do-repositorio>/setup.php
   ```

4. Acesse o sistema:

   ```
   http://localhost/<pasta-do-repositorio>/login.php
   ```

   **Usuário:** `admin`  
   **Senha:** `admin123`

5. Depois de criar o banco, delete o `setup.php` por segurança.

## O que o sistema faz

- Login com senha
- Cadastrar itens (nome, categoria, descricao, preco)
- Listar itens em tabela
- Editar item (sem duplicar)
- Excluir item
