<?php

require("../include/config.php");

remover("subcatlancto", "codsubcatlancto={$_GET['id']}");

header('Location: subcategoria.php');