<?php

include './conectorBD.php';

$conexao = new conexao();
$conexao->dbase = 'gerador_relatorios';
$conexao->conecta();

$titulo = $_POST['titulo'];
$descricao = $_POST['descricao'];
$modulo = $_POST['modulo'];
$sql = $_POST['sql'];

session_start();
$usuario = $_SESSION['usuario'];

$queryrel = 'insert into RELATORIOS (`RELATORIO`, `DESCRICAO`, `SQL`, `ID_MODULO`, `USUARIO`) values("' . $titulo . '", "' . $descricao . '", "' . $sql . '", ' . $modulo . ', "' . $usuario . '")';
$resultsetrel = mysql_query($queryrel);

$queryid = 'SELECT ID FROM RELATORIOS ORDER BY ID DESC LIMIT 1';
$resultsetid = mysql_query($queryid);

$ultimoid = mysql_fetch_row($resultsetid);

$queryfiltros = 'SELECT * FROM FILTROS';
$resultsetfiltros = mysql_query($queryfiltros);

while ($rowfiltros = mysql_fetch_row($resultsetfiltros)) {
    if(isset($_POST[$rowfiltros[0]])){
        $queryfiltrosrel = 'insert into `FILTROS_RELATORIOS` (`ID_RELATORIO`, `ID_FILTRO`) values('.$ultimoid[0].','.$rowfiltros[0].')';
        $resultfiltrosrel = mysql_query($queryfiltrosrel);
    }
}

if ($resultsetrel === FALSE OR $resultfiltrosrel === FALSE) {
    echo '<script>alert("Ocorreu algum erro em meio ao processo de cadastro. Contate setor respons\xE1vel!"' . mysql_error() . ')</script>';
    die(mysql_error());
    echo '<script type="text/javascript">location.href = "relatorios.php";</script>';
} else {
    echo '<script>alert("Relat\xF3rio cadastrado com sucesso!")</script>';
    echo '<script type="text/javascript">location.href = "relatorios.php";</script>';
}

$conexao->desconecta();
