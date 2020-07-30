<?php
// Incluir aquivo de conexão
require("../../include/config.php");

// Recebe o valor enviado
$onde = $_GET['onde'];
$tabela = $_GET['tabela'];
$campos = "favorecido, referencia, CASE WHEN pagrec='P' THEN valorparcela*(-1) ELSE valorparcela END AS valorpago";



//Ver dados de um registro apenas 
$busca = listar($tabela, $campos, $onde, NULL, "3"); 
//var_dump($busca)
//echo $busca[codlancto].":".$busca[favorecido].":".$busca[referencia].":".$busca[valorpago];
echo json_encode($busca);
?>