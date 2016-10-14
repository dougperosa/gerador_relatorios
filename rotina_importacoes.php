<?php

set_time_limit(600);

include './conectorBD.php';

$conexao = new conexao();
$conexao->conecta();
/* Função fopen usada para abrir arquivo, ou seja, joga-lo na memória do servidor, neste caso o arquivo ainda não existe.
  o “w” quer dizer write, que o arquivo pode ser escrito */

$layout = $_POST['importacao'];
$arquivo = fopen($_FILES['arquivo']['tmp_name'], 'r');

if ($layout == 'ctpagar') {

    while (!feof($arquivo)) {
        $linha = fgets($arquivo, 1024);
        if (substr($linha, 0, 3) == '200') {
            $documento = substr($linha, 427, 16);
            $valor = substr($linha, 64, 11);
            $emissao = substr($linha, 30, 8);
            $vencimento = substr($linha, 38, 8);
            $cpf_cnpj = substr($linha, 443, 14);

            if (strlen($cpf_cnpj) == 11) {
                $query_cpf_cnpj = 'SELECT * FROM UNI_PESSOAS WHERE CPF = "' . $cpf_cnpj . '"';
                $resultset_cpf_cnpj = mysql_query($query_cpf_cnpj);
                $row_cpf_cnpj = mysql_fetch_row($resultset_cpf_cnpj);
                $id_pessoa = $row_cpf_cnpj[0];
            } else if (strlen($cpf_cnpj) == 14) {
                $query_cpf_cnpj = 'SELECT * FROM UNI_EMPRESAS WHERE CNPJ = "' . $cpf_cnpj . '"';
                $resultset_cpf_cnpj = mysql_query($query_cpf_cnpj);
                $row_cpf_cnpj = mysql_fetch_row($resultset_cpf_cnpj);
                $id_empresa = $row_cpf_cnpj[0];
            }

            $query = 'INSERT INTO FIN_FINANCEIRO (ID_PESSOA, ID_EMPRESA, NUMERO_DOCUMENTO, TIPO, DATA_EMISSAO, DATA_VENCIMENTO, VALOR_ORIGINAL) '
                    . 'VALUES (' . $id_pessoa . ',' . $id_empresa . ',"' . $documento . '","S","' . $emissao . '","' . $vencimento . '",' . $valor . ')';
            $resultset = mysql_query($query);
        }
    }
} else if ($layout == 'ctreceber') {
    while (!feof($arquivo)) {
        $linha = fgets($arquivo, 1024);
        if (substr($linha, 0, 3) == '200') {
            $documento = substr($linha, 578, 16);
            $valor = substr($linha, 74, 11);
            $emissao = substr($linha, 39, 8);
            $vencimento = substr($linha, 47, 8);
            $cpf_cnpj = substr($linha, 594, 14);

            if (strlen($cpf_cnpj) == 11) {
                $query_cpf_cnpj = 'SELECT * FROM UNI_PESSOAS WHERE CPF = "' . $cpf_cnpj . '"';
                $resultset_cpf_cnpj = mysql_query($query_cpf_cnpj);
                $row_cpf_cnpj = mysql_fetch_row($resultset_cpf_cnpj);
                $id_pessoa = $row_cpf_cnpj[0];
            } else if (strlen($cpf_cnpj) == 14) {
                $query_cpf_cnpj = 'SELECT * FROM UNI_EMPRESAS WHERE CNPJ = "' . $cpf_cnpj . '"';
                $resultset_cpf_cnpj = mysql_query($query_cpf_cnpj);
                $row_cpf_cnpj = mysql_fetch_row($resultset_cpf_cnpj);
                $id_empresa = $row_cpf_cnpj[0];
            }

            $query = 'INSERT INTO FIN_FINANCEIRO (ID_PESSOA, ID_EMPRESA, NUMERO_DOCUMENTO, TIPO, DATA_EMISSAO, DATA_VENCIMENTO, VALOR_ORIGINAL) '
                    . 'VALUES (' . $id_pessoa . ',' . $id_empresa . ',"' . $documento . '","E","' . $emissao . '","' . $vencimento . '",' . $valor . ')';
            $resultset = mysql_query($query);
        }
    }
}
/*
echo '<script>alert("Arquivo importado com Sucesso!")</script>';
echo '<script type="text/javascript">location.href = "layout_importacoes.php";</script>';
