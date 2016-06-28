<?php

set_time_limit(60000);

error_reporting(0);

include './conectorBD.php';

$conexao = new conexao();
$conexao->dbase = 'gerador_relatorios';
$conexao->conecta();

$relatorio = $_POST['relatorio'];

$sql = null;

$queryrel = 'SELECT * FROM RELATORIOS WHERE ID = ' . $relatorio;
$resultsetrel = mysql_query($queryrel);
$rowrel = mysql_fetch_row($resultsetrel);
$titulo = $rowrel[1];
$sql = $rowrel[3];

$queryfiltros = 'SELECT * FROM FILTROS_RELATORIOS WHERE ID_RELATORIO = ' . $relatorio;
$resultsetfiltros = mysql_query($queryfiltros);

if(mysql_num_rows($resultsetfiltros) > 0) {
    while ($rowfiltros = mysql_fetch_row($resultsetfiltros)) {

        $querydadosfiltro = 'SELECT * FROM FILTROS WHERE ID = ' . $rowfiltros[2];
        $resultsetdadosfiltro = mysql_query($querydadosfiltro);
        $rowdadosfiltro = mysql_fetch_row($resultsetdadosfiltro);

        $sql = str_replace($rowdadosfiltro[2], $_POST[$rowdadosfiltro[0]], $sql);
    }
}

$conexao->desconecta();

$conexaounimed = new conexao();
$conexaounimed->conecta(); 

$query = $sql;

$resultset = mysql_query($query);

include './relatorioExcel.php';