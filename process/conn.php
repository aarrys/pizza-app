<?php
    session_start();
    include("process\database_pizzaria_credentials.php");

    $user = 'root';
    $pass = '@T4l3b1984';
    $db = 'pizzaria';
    $host = 'localhost';

    try {

        $conn = new PDO("mysql:host={$host};dbname={$db}", $user, $pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

    } catch (PDOException $e) {
        
        print "Erro: " . $e->getMessage() . "<br/>";
        die();
    
    }

?>