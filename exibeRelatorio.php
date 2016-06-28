<?php

include './cabecalho.php';

$conexao = new conexao();
$conexao->conecta();

$relatorio = $_POST['relatorio'];

$queryrel = 'SELECT * FROM gerador_relatorios.RELATORIOS WHERE ID = '.$relatorio;
$resultsetrel = mysql_query($queryrel);
$rowrel = mysql_fetch_row($resultsetrel);


echo '<div class="container">';
echo '<div class="table table-bordered" style="background-color: rgba(195,200,200,0.5)">';
echo '<div align="center"><b><h3>'.$rowrel[1].'</h3></b><br>'
        . $rowrel[2].'</div><br>';

echo '<form action="geraRelatorio.php" method="POST">';

echo '<input type="hidden" value="'.$relatorio.'" name="relatorio">';

$queryfiltros = 'SELECT * FROM gerador_relatorios.FILTROS_RELATORIOS WHERE ID_RELATORIO = '.$relatorio.' ORDER BY ID_FILTRO';
$resultsetfiltros = mysql_query($queryfiltros);

while($rowfiltros = mysql_fetch_row($resultsetfiltros)){
    $querydadosfiltro = 'SELECT * FROM gerador_relatorios.FILTROS WHERE ID = '.$rowfiltros[2];
    $resultsetdadosfiltro = mysql_query($querydadosfiltro);
    $rowdadosfiltro = mysql_fetch_row($resultsetdadosfiltro);
    
    echo '<div style=" margin-left: 15px">';
    echo '<b>'.$rowdadosfiltro[1].'</b>:<br>';
    echo '<input type="'.$rowdadosfiltro[3].'" name="'.$rowdadosfiltro[0].'" style="height: 30px;" required><br><br>';
    echo '</div>';
}

echo '<div align="center">'
    . '<a href="relatorios.php"><input type="button" class="btn btn-warning" value="Voltar" style="width: 120px"></a>&nbsp'
    . '<input type="submit" class="btn btn-info" value="Gerar" style="width: 120px">'
    . '</div><br>';

echo '</form>';

echo '</div></div>';