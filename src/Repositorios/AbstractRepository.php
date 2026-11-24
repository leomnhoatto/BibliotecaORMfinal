<?php
abstract class AbstractRepository implements RepositoryInterface {
    protected PDO $pdo;
    protected string $tabela;
    public function __construct(PDO $pdo) { $this->pdo = $pdo; }
    abstract protected function hidratar(array $linha): object;
    public function buscar(int $id): ?object {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->tabela} WHERE id = ?");
        $stmt->execute([$id]);
        $linha = $stmt->fetch(PDO::FETCH_ASSOC);
        return $linha ? $this->hidratar($linha) : null;
    }
    public function listar(): array {
        $stmt = $this->pdo->query("SELECT * FROM {$this->tabela} ORDER BY id");
        return array_map(fn($linha) => $this->hidratar($linha), $stmt->fetchAll(PDO::FETCH_ASSOC));
    }
    public function excluir(int $id): void {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->tabela} WHERE id = ?");
        $stmt->execute([$id]);
    }
}
