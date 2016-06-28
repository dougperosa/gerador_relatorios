<?php
include './cabecalho.php';
?>

<div class="container">
    <form method="post" action="rotina_extrato_beneficiario.php">
        <div class="table table-bordered" style="background-color: rgba(195,200,200,0.5)">
            <div align="center"><b><h3>Extrato do Benefici&aacute;rio</h3></b><br></div>
            <div class="container" style="margin-left: 30px">
            <div class="alert alert-warning" style="width: 87%">As Singulares possuem a necessidade de apresentar e detalhar os gastos com a utiliza&ccedil;&atilde;o do plano para os seus
                    benefici&aacute;rios. Para que os benefici&aacute;rios tenham acesso aos seus extratos, foi criado um sistema que interpreta os dados gerados
                    pelas Singulares (importados para o sistema do Portal Unimed pelo do Canal do Benefici&aacute;rio) e os apresenta de maneira
                    estruturada na aplica&ccedil;&atilde;o Web, disponibilizada no Canal do Benefici&aacute;rio.
                    Os layouts s&atilde;o padr&otilde;es que indicam a ordem em que as informa&ccedil;&otilde;es devem ser colocadas nos arquivos de dados para que
                    o sistema do Portal possa compreend&ecirc;las e estrutur&aacute;&ndash;las em seu banco de dados. Em s&iacute;ntese, escrever informa&ccedil;&otilde;es em um
                    arquivo seguindo corretamente um layout &eacute; a garantia de que o mesmo ser&aacute; compreendido por um sistema preparado para
                    faz&ecirc;&ndash;lo.
                </div>
                <br>
                <span>Compet&ecirc;ncia:</span><br>
                <input type="text" name="competencia" placeholder="Digite aqui a compet&ecirc;ncia (ANO + M&Ecirc;S)" style="height: 30px; width: 400px" required><br><br>

                <br><br>
            </div>
            <div align="center">
                <a href="relatorios.php"><input type="button" class="btn btn-danger" style="width: 120px" value="Sair"></a>
                <input type="submit" class="btn btn-info" style="width: 120px" value="Gerar">
            </div><br><br>
        </div>


    </form>
</div>