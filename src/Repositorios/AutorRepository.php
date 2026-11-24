<?php
class AutorRepository extends AbstractRepository {
    protected string $tabela = 'autores'; // Nome fixo: nunca vem do usuário
    protected function hidratar(array $linha): object {
        return new Autor($linha['nome'], (int)$linha['id']);
    }
    public function salvar(object $entidade): void {
        if (!$entidade instanceof Autor) throw new InvalidArgumentException('Esperado Autor');
        if ($entidade->getId() === null) {
            $stmt = $this->pdo->prepare('INSERT INTO autores (nome) VALUES (?)');
            $stmt->execute([$entidade->getNome()]);
            $entidade->definirIdGerado((int)$this->pdo->lastInsertId());
        } else {
            $stmt = $this->pdo->prepare('UPDATE autores SET nome = ? WHERE id = ?');
            $stmt->execute([$entidade->getNome(), $entidade->getId()]);
        }
    }
}
