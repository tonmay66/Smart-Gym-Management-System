<?php
declare(strict_types=1);

function db_connect(): PDO {
  $dsn = "mysql:host=localhost;dbname=smart_gym;charset=utf8mb4";
  return new PDO($dsn, "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false
  ]);
}
