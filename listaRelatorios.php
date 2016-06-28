<?php

$conexao = new conexao();
$conexao->conecta();

session_start();
$setor = $_SESSION['setor'];
$tipo_usuario = $_SESSION['tipo_usuario'];

if ($tipo_usuario == '1') {
    $querymodulos = 'SELECT * FROM gerador_relatorios.MODULOS ORDER BY MODULO ASC';
    $resultsetmodulos = mysql_query($querymodulos);
} else {
    $querymodulos = 'SELECT * FROM gerador_relatorios.MODULOS WHERE MODULO = "' . $setor . '"';
    $resultsetmodulos = mysql_query($querymodulos);
}

echo '<div class="accordion" id="accordion2">';

while ($rowmodulos = mysql_fetch_row($resultsetmodulos)) {
    echo '<div class="accordion-group">';
    echo '<div class="accordion-heading">';
    echo '<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseOne">';
    echo '<b>' . $rowmodulos[1] . '</b>';
    echo '</a>';
    echo '</div>';

    $queryrelatorio = 'SELECT * FROM gerador_relatorios.RELATORIOS WHERE ID_MODULO = ' . $rowmodulos[0] . ' ORDER BY RELATORIO ASC';
    $resultsetrelatorio = mysql_query($queryrelatorio);

    while ($rowrelatorio = mysql_fetch_row($resultsetrelatorio)) {
        echo '<div id="collapse' . $rowrelatorio[0] . '" class="accordion-body collapse in">';
        echo '<div class="accordion-inner">';
        echo '<table style="width:100%"><tr><td width="75%"><b>' . $rowrelatorio[1] . '</b></td></tr>';
        echo '<tr><td width="75%">' . $rowrelatorio[2] . '</td><td width="25%">'
        . '<form action="exibeRelatorio.php" method="POST">'
        . '<input type="hidden" name="relatorio" value="' . $rowrelatorio[0] . '">'
        . '<input type="submit" class="btn btn-success" value="Gerar" style="width: 100px"/>&nbsp'
        . '</form>'
        /* . '<form action="criaRelatorio.php" method="POST">'
          . '<input type="hidden" name="relatorio" value="'.$rowrelatorio[0].'">'
          . '<input type="submit" class="btn btn-warning" value="Editar" style="width: 100px">'
          . '</form>' */
        . '</td></tr>';
        echo '<tr><td width="75%"></td></tr></table>';
        echo '</div>';
        echo '</div>';
    }
    echo '</div>';
}

echo '</div>';
