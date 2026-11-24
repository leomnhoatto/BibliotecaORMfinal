# BibliotecaORM — Programação II
PHP 8.2+, PDO MySQL, MySQL 8 ou MariaDB (XAMPP). Sem Composer.

## Executar no Windows
1. Inicie MySQL no XAMPP. Se a porta 3306 estiver ocupada pelo MySQL80, pare temporariamente esse serviço apenas se não estiver em uso.
2. Para instalação nova, importe sql/schema.sql no phpMyAdmin. Se já usou a versão anterior deste projeto, reutilize o banco (colunas titulo, ano e nome). Não use o schema do tutorial, que possui outras colunas.
3. Abra esta pasta no VS Code (a que contém index.php).
4. No terminal PowerShell: `C:\xampp\php\php.exe index.php`
5. A configuração padrão local usa root sem senha. Se necessário, copie config.example.php para config.php e ajuste. Não versione config.php.

## Evidências manuais
1. Opção 1 duas vezes: Ana Silva e Bruno Costa. Anote IDs. Opção 2: título Introdução à Biblioteca, ano 2024, deixe autores em branco. Anote ID do livro. Capture os cadastros.
2. Opção 5: informe ID do livro. Capture título, ano e ID.
3. Opção 6: informe ID, Biblioteca Atualizada, 2024. Capture nova consulta.
4. Opção 7: informe ID do livro e IDs dos autores separados por vírgula (repita um ID para testar duplicação). Capture coleção de dois autores.
5. Opção 10: capture contagens dos dois repositórios.
6. Opção 11: capture erro esperado e zero livros indevidos.
7. Opção 9: tente excluir um autor vinculado, confirme s; capture bloqueio. Opção 8: exclua livro, confirme s; depois opção 9 para excluir cada autor de teste. Capture resultados.
Opção 4 lista livros; opção 3 lista autores. Opção 12 executa demonstração automática; opção 0 sai. Não misture dados de teste com dados reais.

## Arquitetura
Entidades privadas e validadas; RepositoryInterface define contrato; AbstractRepository compartilha buscar, listar e excluir; repositórios concretos mapeiam e hidratam. LivroRepository usa transação para livro e vínculos. index.php é somente a camada de interação. O esquema simplificado mantém compatibilidade com o projeto anterior. Não há controle de concorrência, carregamento em lote ou interface web. O carregamento de autores por livro pode gerar N+1.

## Entrega
Publique os fontes no Git com histórico real de commits e acesso ao professor. Complete o relatório com capturas reais e exporte para PDF (2–4 páginas). Referências: roteiro e artigo BibliotecaORM fornecidos pelo professor Leandro Otavio Cordova Vieira; código referencial didático. A versão adapta o projeto anterior e adiciona menu CLI.
