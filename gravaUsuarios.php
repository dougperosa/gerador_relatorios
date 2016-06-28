<?php

include './conectorBD.php';

$conexao = new conexao();
$conexao->conecta();

$usuario = $_POST['usuario'];
$nome = $_POST['nome'];
$senha = $_POST['senha'];
$setor = $_POST['setor'];

$querypessoa = 'SELECT IDPESSOA FROM UNI_PESSOAS WHERE NOME LIKE "%'.$nome.'%"';
$resultsetpessoa = mysql_query($querypessoa);
$row = mysql_fetch_row($resultsetpessoa);

$idpessoa = $row[0];

$query = 'insert into UNI_SISTEMAS_USUARIOS (`Nome_Usuario`, `Senha_Usuario`, `ID_PESSOA`, `SETOR`, `ATIVO`, `ID_FILIAL`) values("' . $usuario . '", "' . md5($senha) . '", ' . $idpessoa . ', "' . $setor . '", "S", 1)';
$resultset = mysql_query($query);

if ($resultset === FALSE) {
    echo '<script>alert("Ocorreu algum erro em meio ao processo de cadastro. Contate setor respons\xE1vel!"' . mysql_error() . ')</script>';
    die(mysql_error());
    echo '<script type="text/javascript">location.href = "relatorios.php";</script>';
} else {
    echo '<script>alert("Usu\xE1rio cadastrado com sucesso!")</script>';
    echo '<script type="text/javascript">location.href = "relatorios.php";</script>';
}

$conexao->desconecta();