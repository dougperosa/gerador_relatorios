<html>
    <head>
        <meta charset="utf-8"/>
    </head>

<?php

include './conectorBD.php';

$conexao = new conexao();
$conexao->conecta();

$usuario = $_POST['Usuario'];
$senha = $_POST['Senha'];

$query = 'SELECT U.*, P.NOME FROM unimed.UNI_SISTEMAS_USUARIOS U '
        . 'LEFT JOIN unimed.UNI_PESSOAS P ON U.ID_PESSOA = P.IDPESSOA '
        . 'WHERE Nome_Usuario = "'.$usuario.'" AND Senha_Usuario = MD5("'.$senha.'")';
$resultset = mysql_query($query);
$row = mysql_fetch_array($resultset); 

if(!empty($row[0])){
    
    session_start();
    $_SESSION['usuario'] = $usuario;
    $_SESSION['nome'] = $row[8];
    $_SESSION['tipo_usuario'] = $row[7];
    $_SESSION['setor'] = $row[4];
    
    echo '<script type="text/javascript">location.href="relatorios.php";</script>';
    
}else{
    echo '<script>alert("Usuário e/ou senha incorreto(s)!")</script>';
    echo '<script type="text/javascript">location.href="login.php";</script>';
}

$conexao->desconecta();