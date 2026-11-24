# BibliotecaORM — Programação II
PHP 8.2+, PDO MySQL, MySQL 8 ou MariaDB (XAMPP). Sem Composer.

## Executar no Windows
1. Inicie MySQL no XAMPP. Se a porta 3306 estiver ocupada pelo MySQL80, pare temporariamente esse serviço apenas se não estiver em uso.
2. Para instalação nova, importe sql/schema.sql no phpMyAdmin. Se já usou a versão anterior deste projeto, reutilize o banco (colunas titulo, ano e nome). 

4. No terminal PowerShell: `C:\xampp\php\php.exe index.php`


## Arquitetura
Entidades privadas e validadas; RepositoryInterface define contrato; AbstractRepository compartilha buscar, listar e excluir; repositórios concretos mapeiam e hidratam. LivroRepository usa transação para livro e vínculos. index.php é somente a camada de interação. O esquema simplificado mantém compatibilidade com o projeto anterior. Não há controle de concorrência, carregamento em lote ou interface web. O carregamento de autores por livro pode gerar N+1.

