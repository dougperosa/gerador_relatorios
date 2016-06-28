<?php
include './cabecalho.php';
?>

<div class="container">
    <form method="post" action="gravaFiltros.php">
        <div class="table table-bordered" style="background-color: rgba(195,200,200,0.5)">
            <div class="container" style="margin-left: 30px">
                <br>
                <span>Filtro:</span><br>
                <input type="text" name="filtro" placeholder="Digite aqui o nome do filtro" style="height: 30px; width: 400px"><br><br>
                <span>Par&acirc;metro:</span><br>
                <input type="text" name="parametro" placeholder="Digite aqui o par&acirc;metro para o filtro" style="height: 30px; width: 400px"><br><br>
                <span>Tipo:</span><br>
                <select name="tipo">
                    <option value="date">Data</option>
                    <option value="text">Num&eacute;rico</option>
                    <option value="text">Texto</option>
                    <option value="text">Valor</option>
                </select>
                <br><br>
            </div>
            <div align="center">
                <a href="relatorios.php"><input type="button" class="btn btn-danger" style="width: 120px" value="Sair"></a>
                <input type="submit" class="btn btn-info" style="width: 120px" value="Gravar">
            </div><br><br>
        </div>


    </form>
</div>