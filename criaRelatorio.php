<?php
include './cabecalho.php';
?>
<div class="container">
    <ul class="nav nav-tabs">
        <li id="abaInicio" class="active" onclick="mudaAbaCriaRela('inicio')">
            <a>In&iacute;cio</a>
        </li>
        <li id="abaFiltros" class="inactive" onclick="mudaAbaCriaRela('filtros')"><a href="#">Filtros</a></li>
        <li id="abaSQL" class="inactive" onclick="mudaAbaCriaRela('sql')"><a href="#">SQL</a></li> 
    </ul>

    <form method="post" action="gravaRelatorio.php">
        <div id="criaRelInicio" onload="menuPrincipal('menuCriaRelatorios')">
            <div class="container">
                <br>
                <span class="alert alert-info"><b>Informe T&iacute;tulo e breve descri&ccedil;&atilde;o para o relat&oacute;rio</b></span><br>
                <br>
                <span>T&iacute;tulo do Relat&oacute;rio:</span><br>
                <input type="text" name="titulo" placeholder="Digite aqui o t&iacute;tulo do relat&oacute;rio" style="height: 30px; width: 600px"><br><br>
                <span>Descri&ccedil;&atilde;o:</span><br>
                <textarea name="descricao" placeholder="Digite aqui uma breve descri&ccedil;&atilde;o sobre o relat&oacute;rio" style="height: 120px; width: 800px"></textarea><br><br>
                <span>M&oacute;dulo:</span><br>
                <?php
                $conexao = new conexao();
                $conexao->conecta();

                $querymodulos = 'SELECT * FROM gerador_relatorios.MODULOS ORDER BY MODULO ASC';
                $resultsetmodulos = mysql_query($querymodulos);

                echo '<select name="modulo">';
                while ($row = mysql_fetch_row($resultsetmodulos)) {
                    echo '<option value="' . $row[0] . '">' . $row[1] . '</option>';
                }
                echo '</select>';

                $conexao->desconecta();
                ?>
                <br><br><br>
            </div>
            <div align="center">
                <a href="relatorios.php"><input type="button" class="btn btn-danger" style="width: 120px" value="Sair"></a>
                <input type="button" class="btn btn-info" style="width: 120px" onclick="mudaAbaCriaRela('filtros')" value="Avan&ccedil;ar >>">
            </div>
        </div>

        <div id="criaRelFiltros" style="display: none">
            <div class="container">
                <br>
                <span class="alert alert-info"><b>Selecione os filtros desejados</b></span><br>
                <br>
                <?php
                require './buscaFiltros.php';
                ?>
            </div>
            <div align="center">
                <input type="button" class="btn btn-info" style="width: 120px" onclick="mudaAbaCriaRela('inicio')" value="<< Anterior">
                <input type="button" class="btn btn-info" style="width: 120px"  onclick="mudaAbaCriaRela('sql')" value="Avan&ccedil;ar >>">
            </div>
        </div>

        <div id="criaRelSQL" style="display: none">
            <div class="container">
                <br>
                <span class="alert alert-info"><b>Informe o SQL que ir&aacute; buscar as informa&ccedil;&otilde;es para o relat&oacute;rio</b></span><br>
                <br>
                <span>SQL:</span><br>
                <textarea name="sql" placeholder="Digite aqui o SQL do relat&oacute;rio" style="height: 120px; width: 800px"></textarea><br><br><br>
            </div>
            <div align="center">
                <input type="button" class="btn btn-info" style="width: 120px" onclick="mudaAbaCriaRela('filtros')" value="<< Anterior">
                <input type="submit" class="btn btn-success" style="width: 120px" value="Finalizar">
            </div>
        </div>
    </form>


</div>


