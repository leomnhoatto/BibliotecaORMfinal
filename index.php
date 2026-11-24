<?php
require __DIR__ . '/bootstrap.php';
$autores = new AutorRepository($pdo);
$livros = new LivroRepository($pdo);
function ler(string $texto): string {
    echo $texto;
    $linha = fgets(STDIN);
    if ($linha === false) exit(0);
    return trim($linha);
}
function numero(string $texto): int {
    $valor = filter_var(ler($texto), FILTER_VALIDATE_INT);
    if ($valor === false || $valor <= 0) throw new InvalidArgumentException('Informe um inteiro positivo.');
    return $valor;
}
function mostrarLivro(Livro $l): void {
    echo 'Livro ID: '.$l->getId().' | '.$l->getTitulo().' | Ano: '.$l->getAno().PHP_EOL;
    foreach ($l->getAutores() as $a) echo '  Autor ID: '.$a->getId().' | '.$a->getNome().PHP_EOL;
    echo 'Total de autores: '.count($l->getAutores()).PHP_EOL;
}
function mostrarAutores(AutorRepository $repo): void {
    foreach ($repo->listar() as $a) echo $a->getId().' - '.$a->getNome().PHP_EOL;
}
function buscarLivro(LivroRepository $repo): Livro {
    return $repo->buscar(numero('ID do livro: ')) ?? throw new RuntimeException('Livro não encontrado.');
}
function escolherAutores(Livro $livro, AutorRepository $repo): void {
    mostrarAutores($repo);
    $entrada = ler('IDs dos autores separados por vírgula (vazio para nenhum): ');
    if ($entrada === '') return;
    foreach (explode(',', $entrada) as $id) {
        $id = filter_var(trim($id), FILTER_VALIDATE_INT);
        if ($id === false || $id <= 0) throw new InvalidArgumentException('ID inválido.');
        $autor = $repo->buscar($id) ?? throw new RuntimeException('Autor não encontrado: '.$id);
        $livro->adicionarAutor($autor);
    }
}
function contarRegistros(RepositoryInterface $repo): int { return count($repo->listar()); }
function testarRollback(LivroRepository $livros, AutorRepository $autores): void {
    $id = 1;
    while ($autores->buscar($id) !== null) $id++;
    $titulo = 'Teste rollback '.bin2hex(random_bytes(5));
    $l = new Livro($titulo, 2024);
    $l->adicionarAutor(new Autor('Inexistente', $id));
    try { $livros->salvar($l); throw new RuntimeException('Falha: vínculo inválido aceito.'); }
    catch (PDOException $e) {
        if (($e->errorInfo[1] ?? null) != 1452) throw $e;
        echo 'Erro esperado de chave estrangeira; rollback executado.'.PHP_EOL;
    }
    $total = 0;
    foreach ($livros->listar() as $item) if ($item->getTitulo() === $titulo) $total++;
    echo 'Livros indevidos no banco: '.$total.PHP_EOL;
}
while (true) {
    echo PHP_EOL."=== BIBLIOTECA ORM ===\n1 - Cadastrar autor\n2 - Cadastrar livro\n3 - Listar autores\n4 - Listar livros\n5 - Consultar livro por ID\n6 - Atualizar livro\n7 - Associar autores ao livro\n8 - Excluir livro\n9 - Excluir autor\n10 - Testar polimorfismo\n11 - Testar rollback\n12 - Demonstração automática\n0 - Sair\n";
    $op = ler('Escolha: ');
    try {
        switch ($op) {
            case '0': exit(0);
            case '1':
                $a = new Autor(ler('Nome: ')); $autores->salvar($a);
                echo 'Autor cadastrado. ID: '.$a->getId().PHP_EOL; break;
            case '2':
                $l = new Livro(ler('Título: '), numero('Ano: '));
                escolherAutores($l, $autores); $livros->salvar($l);
                echo 'Livro cadastrado com sucesso.'.PHP_EOL; mostrarLivro($l); break;
            case '3': mostrarAutores($autores); break;
            case '4': foreach ($livros->listar() as $l) mostrarLivro($l); break;
            case '5': mostrarLivro(buscarLivro($livros)); break;
            case '6':
                $l = buscarLivro($livros); $l->setTitulo(ler('Novo título: '));
                $l->setAno(numero('Ano: ')); $livros->salvar($l);
                echo 'Atualizado. Nova consulta:'.PHP_EOL; mostrarLivro($livros->buscar($l->getId())); break;
            case '7':
                $l = buscarLivro($livros); escolherAutores($l, $autores); $livros->salvar($l);
                mostrarLivro($livros->buscar($l->getId())); break;
            case '8':
                $l = buscarLivro($livros);
                if (ler('Confirmar exclusão (s/n)? ') === 's') {
                    $livros->excluir($l->getId()); echo 'Livro removido: '.($livros->buscar($l->getId()) === null ? 'SIM' : 'NÃO').PHP_EOL;
                    echo 'Vínculos removidos; autores preservados.'.PHP_EOL;
                } break;
            case '9':
                $id = numero('ID do autor: ');
                if (!$autores->buscar($id)) throw new RuntimeException('Autor não encontrado.');
                if (ler('Confirmar exclusão (s/n)? ') === 's') { $autores->excluir($id); echo 'Autor removido.'.PHP_EOL; } break;
            case '10': echo 'Livros: '.contarRegistros($livros).PHP_EOL.'Autores: '.contarRegistros($autores).PHP_EOL; break;
            case '11': testarRollback($livros, $autores); break;
            case '12': passthru(escapeshellarg(PHP_BINARY).' '.escapeshellarg(__DIR__.'/demonstracao.php'), $status); echo 'Código de saída: '.$status.PHP_EOL; break;
            default: echo 'Opção inválida.'.PHP_EOL;
        }
    } catch (PDOException $e) {
        echo (($e->errorInfo[1] ?? null) == 1451 ? 'Exclusão bloqueada: autor ainda possui livros vinculados.' : 'Erro no banco: '.$e->getMessage()).PHP_EOL;
    } catch (Throwable $e) { echo 'Erro: '.$e->getMessage().PHP_EOL; }
    ler('Pressione ENTER para voltar ao menu.');
}
