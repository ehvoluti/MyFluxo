<?php

session_start();

$config = array(
	'host'      => 'localhost',
	'banco'     => 'uhgo',
	'usuario'   => 'root',
	'senha'     => '@Matrix12',
	'port'	    => '3306'
);

require_once('banco_mysql.php');
require_once('user.php');
require_once('utils.php');

conectar();
