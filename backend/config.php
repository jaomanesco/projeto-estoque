<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "estoque_db";

// Criar conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}