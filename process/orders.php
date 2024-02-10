<?php

    include_once("conn.php");

    $method = $_SERVER["REQUEST_METHOD"];
    
    if($method === "GET") {

        $pedidosQuery = $conn->query("SELECT * FROM pedidos;");

        $pedidos = $pedidosQuery->fetchAll();

        $pizzas = [];

        //Montando pizza
        foreach($pedidos as $pedido) {

            $pizza = [];

            //Definir um array para a pizza
            $pizza["id"] = $pedido["pizza_id"];

            //Resgatando a pizza
            $pizzaQuery = $conn->prepare("SELECT * FROM pizzas WHERE id = :pizza_id");

            $pizzaQuery->bindParam(":pizza_id", $pizza["id"]);

            $pizzaQuery->execute();

            $pizzaData = $pizzaQuery->fetch(PDO::FETCH_ASSOC);

            //Resgatando a borda da pizza dos pedidos
            $bordaQuery = $conn->prepare("SELECT * FROM bordas WHERE id = :borda_id");

            $bordaQuery->bindParam(":borda_id", $pizzaData["borda_id"]);

            $bordaQuery->execute();

            $borda = $bordaQuery->fetch(PDO::FETCH_ASSOC);

            $pizza["borda"] = $borda["tipo"];

            //Resgatando massa da pizza dos pedidos
            $massaQuery = $conn->prepare("SELECT * FROM massas WHERE id = :massa_id");

            $massaQuery->bindParam(":massa_id", $pizzaData["massa_id"]);

            $massaQuery->execute();

            $massa = $massaQuery->fetch(PDO::FETCH_ASSOC);

            $pizza["massa"] = $massa["tipo"];

            //Resgatando ids dos sabores de pizza dos pedidos
            $saboresQuery = $conn->prepare("SELECT * FROM pizza_sabor WHERE pizza_id = :pizza_id");

            $saboresQuery->bindParam(":pizza_id", $pizza["id"]);

            $saboresQuery->execute();

            $sabores = $saboresQuery->fetchAll(PDO::FETCH_ASSOC);

            //Resgatando nome dos sabores dos pedidos
            $saboresPizza = [];

            $saboresPizzaQuery = $conn->prepare("SELECT * FROM sabores WHERE id = :sabor_id");

            foreach($sabores as $sabor) {
                
                $saboresPizzaQuery->bindParam(":sabor_id", $sabor["sabor_id"]);

                $saboresPizzaQuery->execute();

                $saborPizza = $saboresPizzaQuery->fetch(PDO::FETCH_ASSOC);

                array_push($saboresPizza, $saborPizza["nome"]);

            }

            $pizza["sabores"] = $saboresPizza;
            
            //Adicionar status do pedido
            $pizza["status"] = $pedido["status_id"];

            //Adicionar o array de pizza ao array das pizzas
            array_push($pizzas, $pizza);

        }

        //Resgatando status
        $statusQuery = $conn->query("SELECT * FROM status");

        $status = $statusQuery->fetchAll();
        
    } else if($method === "POST") {
        
        //Verificando tipo de POST (update ou delete)
        $type = $_POST["type"];

        //Deletar pedido
        if($type === "delete") {

            $pizzaId = $_POST["id"];

            $deletePedidoQuery = $conn->prepare("DELETE FROM pedidos WHERE pizza_id = :pizzaId");

            $deletePedidoQuery->bindParam(":pizzaId", $pizzaId, PDO::PARAM_INT);

            $deletePedidoQuery->execute();

            $_SESSION["msg"] = "Pedido removido com sucesso";
            $_SESSION["status"] = "success";

        } else if($type === "update") {

            $pizzaId = $_POST["id"];
            $statusId = $_POST["status"];

            $updateQuery = $conn->prepare("UPDATE pedidos SET status_id = :statusId WHERE pizza_id = :pizzaId");
            $updateQuery->bindParam(":statusId", $statusId, PDO::PARAM_INT,);
            $updateQuery->bindParam(":pizzaId", $pizzaId, PDO::PARAM_INT);

            $updateQuery->execute();

            $_SESSION["msg"] = "Pedido atualizado com sucesso";
            $_SESSION["status"] = "success";
            
        }

        //Retorna usuário para dashboard
        header("Location: ../dashboard.php");

    }

?>