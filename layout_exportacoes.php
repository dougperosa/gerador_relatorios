<?php
include './cabecalho.php';
?>

<div class="container">
    <form method="post" action="rotina_exportacoes.php">
        <div class="table table-bordered" style="background-color: rgba(195,200,200,0.5)">
            <div align="center"><b><h3>Exporta&ccedil;&atilde;o de Dados</h3></b><br></div>
            <div class="container" style="margin-left: 30px">
                <div class="alert alert-warning" style="width: 87%">
                    Selecione abaixo a op&ccedil;&atilde;o de layout para exporta&ccedil;&atilde;o
                </div>
                <table class="table" style="width: 800px;">
                    <tr>
                        <td><b>Filtros:</b><br><br>
                            <label class="radio"><input type="radio" name="expotacao" checked value="ctpagar"/><span>Contas &agrave; Pagar layout TOTVS</span></label><br>
                            <label class="radio"><input type="radio" name="expotacao" value="ctreceber"/><span>Contas &agrave; Receber layout TOTVS</span></label><br>
                            <input type="checkbox" name="mensalidades"/> Incluir Mensalidades<br><br>
                            <input type="checkbox" name="pendencias"/> Incluir Pend&ecirc;ncias<br><br>
                            <input type="checkbox" name="abertos" checked/> Apenas T&iacute;tulos Abertos<br><br>
                            Filial: *<br>
                            <?php
                            $conexao = new conexao();
                            $conexao->dbase = 'unimed';
                            $conexao->conecta();

                            $query = 'SELECT ID, DESCRICAO FROM FIN_FILIAIS';
                            $resultset = mysql_query($query);
                            
                            echo '<select name="filiais" required>';
                            echo '<option></option>';
                            while ($row = mysql_fetch_row($resultset)) {
                                echo '<option value="' . $row[0] . '">' . $row[1] . '</option>';
                            }
                            echo '</select><br>';
                            ?>
                            
                            Tipo de Pend&ecirc;cia:<br>
                            <?php
                            
                            $query = 'SELECT ID, DESCRICAO FROM FIN_TIPOS_PENDENCIA';
                            $resultset = mysql_query($query);
                            
                            echo '<select name="operacao">';
                            echo '<option></option>';
                            while ($row = mysql_fetch_row($resultset)) {
                                echo '<option value="' . $row[0] . '">' . $row[1] . '</option>';
                            }
                            echo '</select><br>';
                            ?>
                            
                            CNPJ/CPF:<br>
                            <input type="text" name="cnpjcpf" placeholder="Informe um cnpj/cpf" style="height: 30px"/><br>
                            Data Emiss&atilde;o:<br>
                            <input type="date" name="dataemissaoinicio" style="height: 30px"/> at&eacute; <input type="date" name="dataemissaofinal" style="height: 30px"/><br>
                            Data Vencimento:<br>
                            <input type="date" name="datavencinicio" style="height: 30px"/> at&eacute; <input type="date" name="datavencfinal" style="height: 30px"/><br>
                        </td>  ,
                    </tr>
                </table>
            </div>
            <div align="center">
                <a href="relatorios.php"><input type="button" class="btn btn-danger" style="width: 120px" value="Sair"></a>
                <input type="submit" class="btn btn-info" style="width: 120px" value="Exportar">
            </div><br><br>
        </div>


    </form>
</div>