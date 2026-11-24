<?php
spl_autoload_register(function (string $classe) {
    foreach (['src/Entidades/', 'src/Repositorios/'] as $pasta) {
        $arquivo = __DIR__ . '/' . $pasta . $classe . '.php';
        if (is_file($arquivo)) { require_once $arquivo; return; }
    }
});
$config = require (is_file(__DIR__ . '/config.php') ? __DIR__ . '/config.php' : __DIR__ . '/config.example.php');
$pdo = new PDO(
    'mysql:host=' . $config['host'] . ';dbname=' . $config['banco'] . ';charset=utf8mb4',
    $config['usuario'], $config['senha'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES => false]
);
