<?php
$servername = "proyecto-db-1";
$username = "root";
$password = "my-secret-pw";
$dbname = "fosdb";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";
?>
