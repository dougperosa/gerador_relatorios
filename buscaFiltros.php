<?php

$conexao = new conexao();
$conexao->conecta();

$query = 'SELECT * FROM gerador_relatorios.FILTROS ORDER BY FILTRO ASC';
$resultset = mysql_query($query);

echo '<table class="table table table-hover"><thead class="alert"><tr><td><b>Filtro</b></td><td><b>Par&acirc;metro</b></td></tr></thead>';
while ($row = mysql_fetch_row($resultset)) {
    echo '<tr><td><input type="checkbox" name="'.$row[0].'" value="'.$row[1].'"><span> '.$row[1].'</span></td>'
            . '<td><span> '.$row[2].'</span></td></tr>';
}
echo '</table>';

$conexao->desconecta();