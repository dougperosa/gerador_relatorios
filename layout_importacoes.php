<?php
include './cabecalho.php';
?>

<div class="container">
    <form method="post" enctype="multipart/form-data" action="rotina_importacoes.php">
        <div class="table table-bordered" style="background-color: rgba(195,200,200,0.5)">
            <div align="center"><b><h3>Importa&ccedil;&atilde;o de Dados</h3></b><br></div>
            <div class="container" style="margin-left: 30px">
                <div class="alert alert-warning" style="width: 87%">
                    Selecione abaixo a op&ccedil;&atilde;o de layout para importa&ccedil;&atilde;o
                </div>
                <table class="table" style="width: 800px;">
                    <tr>
                        <td>
                            <label class="radio"><input type="radio" name="importacao" value="ctpagar"/><span>Contas &agrave; Pagar layout TOTVS</span></label><br>
                            <label class="radio"><input type="radio" name="importacao" value="ctreceber"/><span>Contas &agrave; Receber layout TOTVS</span></label><br>
                        </td>                        
                    </tr>
                    <tr>
                        <td>
                            <b>Arquivo: </b><input type="file" name="arquivo" id="arquivo" style="width: 500px"/>
                        </td>
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