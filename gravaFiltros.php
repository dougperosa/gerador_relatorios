<?php

include './conectorBD.php';

$conexao = new conexao();
$conexao->dbase = 'gerador_relatorios';
$conexao->conecta();

$filtro = $_POST['filtro'];
$parametro = $_POST['parametro'];
$tipo = $_POST['tipo'];

$query = 'insert into FILTROS (`FILTRO`, `PARAMETRO`, `TIPO`) values("' . $filtro . '", "' . $parametro . '", "' . $tipo . '")';
$resultset = mysql_query($query);

if ($resultset === FALSE) {
    echo '<script>alert("Ocorreu algum erro em meio ao processo de cadastro. Contate setor respons\xE1vel!"' . mysql_error() . ')</script>';
    die(mysql_error());
    echo '<script type="text/javascript">location.href = "relatorios.php";</script>';
} else {
    echo '<script>alert("Filtro cadastrado com sucesso!")</script>';
    echo '<script type="text/javascript">location.href = "relatorios.php";</script>';
}

$conexao->desconecta();