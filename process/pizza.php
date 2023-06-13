<?php

include_once("conn.php");
$method = $_SERVER["REQUEST_METHOD"];

//O GET É USADO PARA REGISTRAR DADOS E MONTAGEM DE PEDIDOS
if($method === "GET"){
    $bordasQuery = $conn->query("SELECT * FROM bordas;");
    $bordas = $bordasQuery->fetchAll();

    $massasQuery = $conn->query("SELECT * FROM massas;");
    $massas = $massasQuery->fetchAll();

    $saboresQuery = $conn->query("SELECT * FROM sabores;");
    $sabores = $saboresQuery->fetchAll();

//O POST É USADO PARA A CRIAÇÃO DOS PEDIDOS
 } else if($method === "POST"){
   
    $data = $_POST;

    $borda = $data["borda"];
    $massa = $data["massa"];
    $sabores = $data["sabores"];

   //VALIDAÇÃO MAXIMA DOS SABORES
     if(count($sabores) > 3) {
    $_SESSION["msg"] = "Selecione no maximo 3 sabores!";
    $_SESSION["status"] = "warning";
    }    else{

      //SALVANDO BORDA E MASSA NA PIZZA
      $stmt = $conn->prepare("INSERT INTO pizzas (borda_id, massa_id) VALUES (:borda, :massa)");
   
      //FILTRANDO INPUTS
      $stmt->bindParam(":borda", $borda, PDO::PARAM_INT);
      $stmt->bindParam(":massa", $massa, PDO::PARAM_INT);
      $stmt->execute();

      //RESGATANDO ID DA ULTIMA PIZZA
      
   $pizzaId = $conn->lastInsertId();
   $stmt = $conn->prepare("INSERT INTO pizza_sabor (pizza_id, sabor_id) VALUES (:pizza, :sabor)");

   // REPETIÇÃO ATÉ SALVAR OS SABORES
   foreach($sabores as $sabor){

   //FILTRANDO INPUT
   $stmt->bindParam(":pizza", $pizzaId, PDO::PARAM_INT);
   $stmt->bindParam(":sabor", $sabor, PDO::PARAM_INT);
   $stmt->execute();

   }

   //CRIAR PEDIDO DA PIZZA
   $stmt = $conn->prepare("INSERT INTO pedidos (pizza_id, statu_id ) VALUES (:pizza, :status)");

   //STATUS SEMPRE INICIARA COM 1 POIS ESTA EM PRODUÇÃO 
   $statusId = 1;
   //FILTRANDO INPUTS
   $stmt->bindParam(":pizza", $pizzaId);
   //FILTRANDO INPUTS
   $stmt->bindParam(":status", $statusId);
   $stmt->execute();

   $_SESSION["msg"] = "Pedido realizado com sucesso! :)";
   $_SESSION["status"] = "success";
}



   //RETONAR PARA A PAGINA INICIAL
   header("Location: ..");
}
?>