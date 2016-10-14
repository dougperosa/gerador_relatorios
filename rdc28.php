<?php
include './cabecalho.php';

$conexao = new conexao();
$conexao->dbase = 'unimed';
$conexao->conecta();
?>

<div class="container">
    <form method="post" action="rotina_rdc28.php">
        <div class="table table-bordered" style="background-color: rgba(195,200,200,0.5)">
            <div align="center"><b><h3>RDC 28</h3></b><br></div>
            <div class="container" style="margin-left: 30px">
                <div class="alert alert-warning" style="width: 87%">
                </div>
                <br>
                <div style="margin-left: 10px">
                    <span>Plano(s):</span><br>
                    <input type="text" name="planos" placeholder="Digite aqui o(s) plano(s)" style="height: 30px; width: 750px" >
                </div>
                <br>

                <div style="margin-left: 10px">
                    <span>Contrato ANS:</span><br>
                    <?php
                    $query = 'SELECT ID, REGISTRO, NOME FROM UNI_CONTRATO_ANS ORDER BY 2 ASC';
                    $resultset = mysql_query($query);

                    echo '<select name="contrato_ans" style="width: 750px">';
                    echo '<option></option>';
                    while ($row = mysql_fetch_row($resultset)) {
                        echo '<option value="' . $row[0] . '">' . $row[1] . ' - ' . $row[2] . '</option>';
                    }
                    echo '</select><br>';
                    ?>
                </div>
                <br>

                <table class="table" style="width: 800px;">
                    <tr>
                        <td><span>Grupo de Plano:</span></td>
                        <td><span>Grupo de Pool:</span></td>
                    </tr>
                    <tr>
                        <td>
                            <?php
                            $query = 'SELECT ID, DESCRICAO FROM UNI_GRUPOS_EMPRESA ORDER BY 2 ASC';
                            $resultset = mysql_query($query);

                            echo '<select name="grupo_planos">';
                            echo '<option></option>';
                            while ($row = mysql_fetch_row($resultset)) {
                                echo '<option value="' . $row[0] . '">' . $row[1] . '</option>';
                            }
                            echo '</select><br>';
                            ?>
                        </td>
                        <td>
                            <?php
                            $query = 'SELECT ID, DESCRICAO FROM UNI_GRUPOS_POOL ORDER BY 2 ASC';
                            $resultset = mysql_query($query);

                            echo '<select name="grupo_pool">';
                            echo '<option></option>';
                            while ($row = mysql_fetch_row($resultset)) {
                                echo '<option value="' . $row[0] . '">' . $row[1] . '</option>';
                            }
                            echo '</select><br>';
                            ?>
                        </td>
                    <tr>
                        <td><span>Data Renova&ccedil;&atilde;o Inicial:</span></td>
                        <td><span>Data Renova&ccedil;&atilde;o Final:</span></td>
                    </tr>
                    <tr>
                        <td><input type="date" name="renovacao_inicio" placeholder="Digite aqui data de renova&ccedil;&atilde;o inicial" style="height: 30px;" ></td>
                        <td><input type="date" name="renovacao_final" placeholder="Digite aqui data de renova&ccedil;&atilde;o final" style="height: 30px;" ></td>
                    </tr>
                    <tr>
                        <td><span>Data Inicial:</span></td>
                        <td><span>Data Final:</span></td>
                    </tr>
                    <tr>
                        <td><input type="date" name="data_inicio" placeholder="Digite aqui data inicial" style="height: 30px;" required></td>
                        <td><input type="date" name="data_final" placeholder="Digite aqui data final" style="height: 30px;" required></td>
                    </tr>
                </table>
                <br><br>
            </div>
            <div align="center">
                <a href="relatorios.php"><input type="button" class="btn btn-danger" style="width: 120px" value="Sair"></a>
                <input type="submit" class="btn btn-info" style="width: 120px" value="Gerar">
            </div><br><br>
        </div>


    </form>
</div>