<?php
require __DIR__ . '/bootstrap.php';
function mostrarRepositorio(RepositoryInterface $repositorio): void {
    echo 'Total de registros: ' . count($repositorio->listar()) . PHP_EOL;
}
function linha(string $mensagem): void { echo PHP_EOL . '=== ' . $mensagem . ' ===' . PHP_EOL; }
$autores = new AutorRepository($pdo);
$livros = new LivroRepository($pdo);
try {
    linha('1 - INSERT autores e livro (IDs gerados pelo MySQL)');
    $a1 = new Autor('Ana Silva'); $a2 = new Autor('Bruno Costa');
    $autores->salvar($a1); $autores->salvar($a2);
    echo "Autores IDs: {$a1->getId()} e {$a2->getId()}" . PHP_EOL;
    echo 'SELECT autor: ' . $autores->buscar($a1->getId())->getNome() . PHP_EOL;
    $livro = new Livro('Introdução à Biblioteca', 2024);
    $livro->adicionarAutor($a1); $livro->adicionarAutor($a2);
    $livro->adicionarAutor($a1); // duplicação ignorada
    $livros->salvar($livro);
    echo 'Livro ID: ' . $livro->getId() . PHP_EOL;
    linha('2 - SELECT por ID e hidratação');
    $lido = $livros->buscar($livro->getId());
    echo $lido->getTitulo() . ' (' . $lido->getAno() . ')' . PHP_EOL;
    linha('3 - UPDATE e nova busca');
    $lido->setTitulo('Biblioteca Atualizada'); $livros->salvar($lido);
    echo $livros->buscar($livro->getId())->getTitulo() . PHP_EOL;
    linha('4 - Relação N:N (dois autores sem duplicação)');
    foreach ($livros->buscar($livro->getId())->getAutores() as $autor) echo $autor->getNome() . PHP_EOL;
    linha('5 - Polimorfismo (mesma função, dois repositórios)');
    mostrarRepositorio($livros); mostrarRepositorio($autores);
    linha('6 - Rollback intencional: vínculo com autor inexistente');
    $tituloTeste = 'Teste rollback ' . bin2hex(random_bytes(5));
    $invalido = new Livro($tituloTeste, 2025);
    $idInexistente = 1;
    while ($autores->buscar($idInexistente) !== null) $idInexistente++;
    $invalido->adicionarAutor(new Autor('Autor inexistente', $idInexistente));
    try { $livros->salvar($invalido); }
    catch (PDOException $erro) { if (($erro->errorInfo[1] ?? null) != 1452) throw $erro; echo 'Erro esperado de chave estrangeira; rollback executado.' . PHP_EOL; }
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM livros WHERE titulo = ?');
    $stmt->execute([$tituloTeste]);
    echo 'Livros indevidos no banco: ' . $stmt->fetchColumn() . PHP_EOL;
    linha('7 - DELETE controlado');
    $livros->excluir($livro->getId());
    echo 'Livro removido: ' . ($livros->buscar($livro->getId()) === null ? 'SIM' : 'NÃO') . PHP_EOL;
    $autores->excluir($a1->getId()); $autores->excluir($a2->getId());
    echo 'Autores removidos após excluir os vínculos.' . PHP_EOL;
    echo 'Observação: FK RESTRICT impede excluir autor que ainda tenha vínculos.' . PHP_EOL;
} catch (Throwable $erro) {
    echo 'Falha na demonstração: ' . $erro->getMessage() . PHP_EOL;
    exit(1);
}
