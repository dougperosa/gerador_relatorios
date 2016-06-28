<?php
include './cabecalho.php';

$conexao = new conexao();
$conexao->dbase = 'gerador_relatorios';
$conexao->conecta();

?>

<div class="container">
    <form method="post" action="gravaUsuarios.php">
        <div class="table table-bordered" style="background-color: rgba(195,200,200,0.5)">
            <div class="container" style="margin-left: 30px">
                <br>
                <span>Usu&aacute;rio:</span><br>
                <input type="text" name="usuario" placeholder="Digite aqui nome do usu&aacute;rio" style="height: 30px; width: 400px" required><br><br>
                <span>Nome:</span><br>
                <input type="text" name="nome" placeholder="Digite aqui seu nome" style="height: 30px; width: 400px" required><br><br>
                <span>Senha:</span><br>
                <input type="password" name="senha" id="senha" placeholder="Digite aqui sua senha" style="height: 30px; width: 300px" required><br><br>
                <span>Confirme a Senha:</span><br>
                <input type="password" name="confirmasenha" id="confirmasenha" placeholder="Digite aqui sua senha novamente" onblur="validaConfirmacao()" style="height: 30px; width: 300px" required><br><br>
                <span>Setor:</span><br>
                <select name="setor" required>
                    <?php
                    $query = 'SELECT MODULO FROM MODULOS ORDER BY MODULO ASC';
                    $resultset = mysql_query($query);

                    while ($row = mysql_fetch_row($resultset)) {
                        echo '<option value="'.$row[0].'">'.$row[0].'</option>';
                    }
                    ?>

                </select><br><br>
            </div>
            <div align="center">
                <a href="relatorios.php"><input type="button" class="btn btn-danger" style="width: 120px" value="Sair"></a>
                <input type="submit" class="btn btn-info" style="width: 120px" value="Gravar">
            </div><br><br>
        </div>
    </form>
</div>