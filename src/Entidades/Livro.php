<?php
class Livro {
    private ?int $id;
    private string $titulo;
    private int $ano;
    private array $autores = [];
    public function __construct(string $titulo, int $ano, ?int $id = null) {
        $this->setTitulo($titulo);
        $this->setAno($ano);
        if ($id !== null && $id <= 0) throw new InvalidArgumentException('ID inválido');
        $this->id = $id;
    }
    public function getId(): ?int { return $this->id; }
    public function getTitulo(): string { return $this->titulo; }
    public function getAno(): int { return $this->ano; }
    public function getAutores(): array { return $this->autores; }
    public function setTitulo(string $titulo): void {
        if (trim($titulo) === '' || preg_match_all('/./us', trim($titulo)) > 150) throw new InvalidArgumentException('Título obrigatório');
        $this->titulo = trim($titulo);
    }
    public function setAno(int $ano): void {
        if ($ano < 1 || $ano > (int)date('Y') + 1) throw new InvalidArgumentException('Ano inválido');
        $this->ano = $ano;
    }
    public function adicionarAutor(Autor $autor): void {
        if ($autor->getId() === null) throw new InvalidArgumentException('Salve o autor antes');
        foreach ($this->autores as $existente) {
            if ($existente->getId() === $autor->getId()) return;
        }
        $this->autores[] = $autor;
    }
    public function definirIdGerado(int $id): void {
        if ($this->id !== null || $id <= 0) throw new LogicException('ID não pode ser alterado');
        $this->id = $id;
    }
}
