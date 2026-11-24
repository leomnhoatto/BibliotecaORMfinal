<?php
class Autor {
    private ?int $id;
    private string $nome;
    public function __construct(string $nome, ?int $id = null) {
        $this->setNome($nome);
        if ($id !== null && $id <= 0) throw new InvalidArgumentException('ID inválido');
        $this->id = $id;
    }
    public function getId(): ?int { return $this->id; }
    public function getNome(): string { return $this->nome; }
    public function setNome(string $nome): void {
        if (trim($nome) === '' || preg_match_all('/./us', trim($nome)) > 120) throw new InvalidArgumentException('Nome obrigatório');
        $this->nome = trim($nome);
    }
    // Usado pelo repositório após INSERT (não há setter público de ID).
    public function definirIdGerado(int $id): void {
        if ($this->id !== null || $id <= 0) throw new LogicException('ID não pode ser alterado');
        $this->id = $id;
    }
}
