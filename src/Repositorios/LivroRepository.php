<?php
class LivroRepository extends AbstractRepository {
    protected string $tabela = 'livros';
    protected function hidratar(array $linha): object {
        $livro = new Livro($linha['titulo'], (int)$linha['ano'], (int)$linha['id']);
        $stmt = $this->pdo->prepare('SELECT a.id, a.nome FROM autores a JOIN livros_autores la ON la.autor_id = a.id WHERE la.livro_id = ? ORDER BY a.id');
        $stmt->execute([$livro->getId()]);
        $vistos = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $autor) {
            $id = (int)$autor['id'];
            if (!isset($vistos[$id])) {
                $livro->adicionarAutor(new Autor($autor['nome'], $id));
                $vistos[$id] = true;
            }
        }
        return $livro;
    }
    public function salvar(object $entidade): void {
        if (!$entidade instanceof Livro) throw new InvalidArgumentException('Esperado Livro');
        // Livro e seus vínculos são salvos como uma única operação.
        $this->pdo->beginTransaction();
        try {
            if ($entidade->getId() === null) {
                $stmt = $this->pdo->prepare('INSERT INTO livros (titulo, ano) VALUES (?, ?)');
                $stmt->execute([$entidade->getTitulo(), $entidade->getAno()]);
                $novoId = (int)$this->pdo->lastInsertId();
            } else {
                $stmt = $this->pdo->prepare('UPDATE livros SET titulo = ?, ano = ? WHERE id = ?');
                $stmt->execute([$entidade->getTitulo(), $entidade->getAno(), $entidade->getId()]);
            }
            $this->pdo->prepare('DELETE FROM livros_autores WHERE livro_id = ?')->execute([$novoId ?? $entidade->getId()]);
            $stmt = $this->pdo->prepare('INSERT INTO livros_autores (livro_id, autor_id) VALUES (?, ?)');
            foreach ($entidade->getAutores() as $autor) {
                $stmt->execute([$novoId ?? $entidade->getId(), $autor->getId()]);
            }
            $this->pdo->commit();
            if (isset($novoId)) $entidade->definirIdGerado($novoId);
        } catch (Throwable $erro) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            throw $erro;
        }
    }
    public function excluir(int $id): void {
        $this->pdo->beginTransaction();
        try {
            $this->pdo->prepare('DELETE FROM livros_autores WHERE livro_id = ?')->execute([$id]);
            parent::excluir($id);
            $this->pdo->commit();
        } catch (Throwable $erro) {
            $this->pdo->rollBack();
            throw $erro;
        }
    }
}
