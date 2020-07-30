<?php
//header("Content-type: text/html; charset=ISO-8859-1");
/**
 * Nesta função, vamos gerenciar a conexão com um banco de dados, direto de nosso
 * arquivo de configuração com informações do banco.
 */

function conectar() {
	global $config;
//	var_dump($config);
	
	//mysql_connect($config['host'], $config['usuario'], $config['senha']);
//	mysql_select_db($config['banco']);
    try {
        $conexao = new PDO('mysql:host=localhost;dbname=uhgo', 'root', '@Matrix12');
        return $conexao;
    } catch(PDOException $e) {
        die('Erro: ' . $e->getMessage());
    }

}

/**
 * Nesta função, simplificamos a maneira de inserir dados em uma tabela.
 *
 * @param string $tabela Nome da tabela a receber dados
 * @param array $dados Dados a serem inseridos na tabela, em forma de um array multi-dimensional
 */
function inserir($tabela, $dados) {
	/**
	 * Para cada chave e valor em nosso array, criamos dois novos arrays.
	 * Um com colunas, outro com valores.
	 *
	 * $valores é um array com os valores a serem inseridos, envolvidos em aspas simples: 'lorem ipsum'.
	 * Logo abaixo usamos implode para transformar esses valores em uma string separada
	 * por vírgulas: 'lorem ipsum', 'dolor sit amet', 'nepet quisquam'
	 *
	 * Depois, basta jogar essa string na nossa query.
	 */
	foreach($dados as $coluna => $valor) {
		$colunas[] = "$coluna"; // Envolvemos o valor em crases para evitar erros na query SQL
		$valores[] = "'$valor'";
	}
	
	/**
	 * Transformamos nosso array de colunas em uma string, separada por vírgulas
	 */
	$colunas = implode(", ", $colunas);
	
	/**
	 * Transformamos nosso array de substitutos em uma string, separada por vírgulas
	 */
	$valores = implode(", ", $valores);
	
	/**
	 * Montamos nossa query SQL
	 */
	$query = "INSERT INTO $tabela ($colunas) VALUES ($valores)";
   
	//echo $query;

	$conexao = conectar();
	// print_r(get_class_methods($conexao));

	if($conexao->exec($query)) {
	    $id = $conexao->lastInsertId();
	    //echo "Novo cadastro com id $id.";
	} else {
	    echo $conexao->errorCode() . "<br>";
	    print_r($conexao->errorInfo());
	}

	

	/**
	 * Preparamos e executamos nossa query
	 */
        return "OK";
        //$conexao->close();
        
}

/**
 * Nesta função, simplificamos a maneira de alterar dados em uma tabela.
 *
 * @param string $tabela Nome da tabela a ter dados alterados
 * @param string $onde Onde os dados serão alterados
 * @param array $dados Dados a serem alterados na tabela, em forma de um array multi-dimensional
 */
function alterar($tabela, $onde, $dados) {
	
	/**
	 * Pegaremos os valores e campos recebidos no método e os organizaremos
	 * de modo que fique mais fácil montar a query logo a seguir
	 */
	foreach($dados as $coluna => $valor) {
		$set[] = "`$coluna` = '$valor'";
	}
	
	/**
	 * Transformamos nosso array de valores em uma string, separada por vírgulas
	 */
	$set = implode(", ", $set);
	
	/**
	 * Montamos nossa query SQL
	 */
	$query = "UPDATE `$tabela` SET $set WHERE $onde";
	
	/**
	 * Preparamos e executamos nossa query
	 */
	return mysql_query($query);
}

/**
 * Nesta função, simplificamos a maneira de remover dados de uma tabela.
 *
 * @param string $tabela Nome da tabela a ter dados removidos
 * @param string $onde Onde os dados serão removidos
 */
function remover($tabela, $onde = null) {

	/**
	 * Montamos nossa query SQL
	 */
	$query = "DELETE FROM `$tabela`";
	
	/**
	 * Caso tenhamos um valor de onde deletar dados, adicionamos a cláusula WHERE
	 */
	if(!empty($onde)) {
		$query .= " WHERE $onde";
	}

	//echo $query;
	
	//$consulta = mysql_query($query);


$conexao = conectar();
$stmt = $conexao->prepare($query);

//Não entedi muito bem esse IF, mas acho que o importarnte foi rodar o "$stmt->execute"
if($stmt->execute([':id' => 14])) {
    return "Sucesso :)";
} else {
    echo "Erro :(";
    print_r($stmt->errorInfo());
}

}

/**
 * Nesta função, simplificamos a maneira de consultar dados de uma tabela.
 *
 * @param string $tabela Nome da tabela a ter dados consultados
 * @param string $campos Quais campos serão selecionados na tabela
 * @param string $onde Onde os dados serão consultados
 * @param string $ordem Ordem dos dados a serem consultados
 * @param string $filtro Filtrar dados consultados por conter uma palavra
 * @param string $limite Limitar dados consultados
 */
function listar($tabela, $campos, $onde = null, $filtro = null, $ordem = null, $limite = null) {
	
	/**
	 * Montamos nossa query SQL
	 */
	$query = "SELECT $campos FROM `$tabela`";
	
	/**
	 * Caso tenhamos um valor de onde selecionar dados, adicionamos a cláusula WHERE
	 */
	if(!empty($onde)) {
		$query .= " WHERE $onde";
	}
	
	/**
	 * Caso tenhamos um valor de como filtrar dados que contenham uma regra, adicionamos a cláusula LIKE
	 */
	if(!empty($filtro)) {
		$query .= " LIKE $filtro";
	}
	
	/**
	 * Caso tenhamos um valor de como ordenar dados, adicionamos a cláusula ORDER BY
	 */
	if(!empty($ordem)) {
		$query .= " ORDER BY $ordem";
	}
	
	/**
	 * Caso tenhamos um valor de como limitar os dados consultados, adicionamos a cláusula LIMIT
	 */
	if(!empty($limite)) {
		$query .= " LIMIT $limite";
	}

//echo $query;

$conexao = conectar();

//$stmt = $conexao->prepare($query);
//$resultado = $conexao->query($query)->fetchAll(PDO::FETCH_ASSOC);


$resultado = $conexao->query($query)->fetchAll(PDO::FETCH_ASSOC);
//var_dump($resultado[0]);

foreach ($resultado as $chave => $row){
	//foreach ($resultado[$chave] as $value) {
			$resultados[] = $resultado[$chave];
	//}
}

//var_dump($resultados);



return $resultados;


}

/**
 * Nesta função, simplificamos a maneira de consultar apenas um dado de uma tabela
 *
 * @param string $tabela Nome da tabela a ter dados consultados
 * @param string $campos Quais campos serão selecionados na tabela
 * @param string $onde Onde os dados serão consultados
 */
function ver($tabela, $campos, $onde) {
	
	/**
	 * Montamos nossa query SQL para pegar apenas um dado
	 */
	$sql = "SELECT $campos FROM $tabela";
	
	/**
	 * Selecionamos onde queremos pegar este dado
	 */
	if(!empty($onde)) {
		$sql .= " WHERE $onde";
	}
	
	/**
	 * Limitamos para apenas 1 resultado
	 */
	$sql .= " LIMIT 1;";

        /**
	 * Preparamos e executamos nossa query
	 */
	//$consulta = mysql_query($query);
$conexao = conectar();
//echo $sql;

$resultado = $conexao->query($sql)->fetchAll(PDO::FETCH_ASSOC);

	return $resultado[0];
}



function buscapreco($campos) {
	
	/**
	 * Apenas função para buscar preço na tabela
	 */
	$sql = "SELECT buscapreco($campos)";
	$conexao = conectar();
	//echo $slquery;

	$resultado = $conexao->query($sql)->fetchAll(PDO::FETCH_ASSOC);
	return $resultado[0];
}

function geraid($tabela, $campo) {
	
	/**
	 * Montamos nossa query SQL para pegar apenas um dado
	 */
	$query = "SELECT MAX($campo)+1 AS newid FROM $tabela";

$conexao = conectar();
//echo $query;

$resultado = $conexao->query($query)->fetchAll(PDO::FETCH_ASSOC);

return $resultado[0];


}


function combocat($tabela, $campos, $onde = null) {
	
	/**
	 * Montamos nossa query SQL para pegar apenas um dado
	 */
	$query = "SELECT $campos FROM $tabela WHERE $onde";
	//echo $query;

$conexao = conectar();


$resultado = $conexao->query($query)->fetchAll(PDO::FETCH_ASSOC);

foreach ($resultado as $chave => $row){
			$resultados[] = $resultado[$chave];
}


return $resultados;

}


function saldo($banco) {
	
	/**
	 * Montamos nossa query SQL para pegar apenas um dado
	 */
	$query = "SELECT (SELECT nome FROM banco WHERE codbanco=$banco) AS banco, SUM(CASE WHEN pagrec='P' THEN (valorparcela*(-1)) ELSE valorparcela END) AS saldo FROM lancamento WHERE codbanco = $banco";
	
//echo $query;
$conexao = conectar();


$resultado = $conexao->query($query)->fetchAll(PDO::FETCH_ASSOC);

	return $resultado[0];
}






function grafico($tipo, $filtro_ano, $filtro_mes, $filtro_categoria) {

	if (STRLEN($filtro_ano)>5) {
    	$filtro=$filtro_ano; 
	} else {
			$filtro="(EXTRACT(YEAR FROM CURRENT_DATE)) AND (EXTRACT(YEAR FROM CURRENT_DATE))";
	} 

	if (STRLEN($filtro_mes)>5) {
    	$filtro_mes=$filtro_mes; 
	} else {
			$filtro_mes="(EXTRACT(MONTH FROM CURRENT_DATE)) AND (EXTRACT(MONTH FROM CURRENT_DATE)) ";
	} 

	$categoria = "TRUE";
	if (STRLEN($filtro_categoria)>0) {
		$categoria ="lancamento.codcatlancto =". $filtro_categoria; 
	} 


	switch ($tipo) {
	case "SUBCATEGORIA":
    	$query = "SELECT CASE WHEN SUBSTR(subcatlancto.descricao,2,1)='.' THEN SUBSTR(subcatlancto.descricao,3,9) ELSE subcatlancto.descricao END AS label, ROUND(SUM(CASE WHEN pagrec='R' THEN 			valorparcela*(-1) ELSE valorparcela END),0) AS value
    		,CONCAT('j-showAlert-','codcatlancto=',lancamento.codcatlancto,' AND codsubcatlancto=',lancamento.codsubcatlancto, ' AND (EXTRACT(YEAR FROM dtemissao)) BETWEEN $filtro AND EXTRACT(MONTH FROM dtemissao) BETWEEN  $filtro_mes AND codcatlancto NOT IN (SELECT codcatlancto FROM catlancto WHERE debcred=''C'')') as link
					FROM lancamento 
					INNER JOIN subcatlancto ON (lancamento.codsubcatlancto = subcatlancto.codsubcatlancto) 
					INNER JOIN catlancto ON (lancamento.codcatlancto = catlancto.codcatlancto) 
					WHERE  (EXTRACT(YEAR FROM dtemissao)) BETWEEN $filtro
					AND EXTRACT(MONTH FROM dtemissao) BETWEEN $filtro_mes
					AND SUBSTR(catlancto.descricao,1,2)<>'X.' 
					AND $categoria
					GROUP BY 1,3 ORDER BY 2 DESC ";

		break;
	case "CATEGORIA":
		$query = "					
					SELECT CASE WHEN SUBSTR(catlancto.descricao,2,1)='.' THEN SUBSTR(catlancto.descricao,3,9) ELSE catlancto.descricao END AS label
					,ROUND(SUM(CASE WHEN pagrec='R' THEN 	valorparcela*(-1) ELSE valorparcela END),0) AS value
					,CONCAT('j-showAlert-','codcatlancto=',lancamento.codcatlancto, ' AND (EXTRACT(YEAR FROM dtemissao)) BETWEEN $filtro AND EXTRACT(MONTH FROM dtemissao) BETWEEN $filtro_mes 
						AND codcatlancto NOT IN (SELECT codcatlancto FROM catlancto WHERE debcred=''C'') 
						AND $categoria') as link
					FROM lancamento 
					INNER JOIN catlancto ON (lancamento.codcatlancto = catlancto.codcatlancto) 
					WHERE  (EXTRACT(YEAR FROM dtemissao)) BETWEEN $filtro
					AND EXTRACT(MONTH FROM dtemissao) BETWEEN $filtro_mes
					AND SUBSTR(catlancto.descricao,1,2)<>'X.' 
					GROUP BY 1,3 ORDER BY 2 DESC 
					";

		break;
	case "ANO":
		$query = "
					SELECT CONCAT('Ano:',EXTRACT(YEAR FROM dtemissao)) AS label, 
					ROUND(SUM(CASE WHEN pagrec='R' THEN valorparcela*(-1) ELSE valorparcela END),0) AS value
					,CONCAT('j-showAlert-',' (EXTRACT(YEAR FROM dtemissao))=',EXTRACT(YEAR FROM dtemissao),' AND EXTRACT(MONTH FROM dtemissao) BETWEEN  $filtro_mes  
					AND codcatlancto NOT IN (SELECT codcatlancto FROM catlancto WHERE debcred=''C'') 
					AND $categoria')  as link
					FROM lancamento 
					INNER JOIN catlancto ON (lancamento.codcatlancto = catlancto.codcatlancto) 
					WHERE  (EXTRACT(YEAR FROM dtemissao)) BETWEEN $filtro
					AND EXTRACT(MONTH FROM dtemissao) BETWEEN $filtro_mes
					AND SUBSTR(catlancto.descricao,1,2)<>'X.'
					AND $categoria
					GROUP BY 1, EXTRACT(YEAR FROM dtemissao) ORDER BY 1
				";

		break;
	case "MES":
		$query = "
					SELECT CONCAT('Mes:',LPAD(CAST(EXTRACT(MONTH FROM dtemissao) AS CHAR(2)),2,'0')) AS label, 
					ROUND(SUM(CASE WHEN pagrec='R' THEN valorparcela*(-1) ELSE valorparcela END),0) AS value 
					,CONCAT('j-showAlert-',' (EXTRACT(YEAR FROM dtemissao)) BETWEEN $filtro',' AND EXTRACT(MONTH FROM dtemissao)=',EXTRACT(MONTH FROM dtemissao), ' 
					AND codcatlancto NOT IN (SELECT codcatlancto FROM catlancto WHERE debcred=''C'')
					AND $categoria ') as link
					FROM lancamento 
					INNER JOIN catlancto ON (lancamento.codcatlancto = catlancto.codcatlancto) 
					WHERE  (EXTRACT(YEAR FROM dtemissao)) BETWEEN $filtro
					AND EXTRACT(MONTH FROM dtemissao) BETWEEN $filtro_mes
					AND SUBSTR(catlancto.descricao,1,2)<>'X.'
					AND $categoria
					GROUP BY 1,EXTRACT(MONTH FROM dtemissao) ORDER BY 1 
				";

		break;
	}

//echo $query;
$conexao = conectar();

//$stmt = $conexao->prepare($query);
//$resultado = $conexao->query($query)->fetchAll(PDO::FETCH_ASSOC);


$resultado = $conexao->query($query)->fetchAll(PDO::FETCH_ASSOC);
//var_dump($resultado[0]);
$total=0;
foreach ($resultado as $chave => $row){
		$resultados[] = $resultado[$chave];
		//var_dump($resultado);
		$total+= $resultado[$chave]["value"];
}
//echo $total;

	$retorno = json_encode($resultados);
	//echo $retorno;
	return 	array($retorno,$total);	
}
