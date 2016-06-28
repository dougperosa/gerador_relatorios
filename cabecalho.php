<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <!--<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />-->
        <title>Gerador de Relat&oacute;rios Unimed Erechim</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

        <meta name="description" content="Gerador de Relat&oacute;rios Unimed Erechim"/>
        <meta name="author" content="Douglas Perosa"/>

        <link rel="shortcut icon" href="imagens/favicon.ico"/>

        <link href="bootstrap/css/bootstrap.css" type="text/css" rel="stylesheet" />

        <script type="text/javascript" src="js/functions.js"></script>
        <script type="text/javascript" src="bootstrap/js/bootstrap.js"></script>
    </head>
    <body>
        <div class="container" style="height: 150px; background-color: #00995D; border-radius: 6px;">
            <table width="100%" border="0">
                <tr>
                    <td width="26%"><a href=""><img src="./imagens/logo.png" id="logo_cabecalho" border="0" style="position: absolute; z-index: 1; width: 187px; height: 115px; margin-left: 30px; margin-top: 5px "></a></td>
                </tr>
            </table>
            <div align="right" style="margin-right: 15px; margin-top: 10px" >
                <?php
                error_reporting(0);

                session_start();

                if (!empty($_SESSION['nome'])) {

                    echo '<font style="color: white"><b>Bem vindo, ' . $_SESSION['nome'] . '&nbsp&nbsp</b></font>';
                }else{
                    echo '<script type="text/javascript">location.href = "login.php";</script>';
                }

                include './conectorBD.php';
                ?>
            </div>
        </div>
        <div class="container navbar-wrapper">

            <div class="navbar navbar-inverse">
                <div class="navbar-inner">
                    <!-- Responsive Navbar Part 1: Button for triggering responsive navbar (not covered in tutorial). Include responsive CSS to utilize. -->
                    <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </a>
                    <a class="brand" href="#">Gerador de Relat&oacute;rios</a>
                    <div class="nav-collapse collapse" align="center">
                        <ul class="nav">
                            <li style="width: 160px" id="menuRelatorios"><a href="relatorios.php">Relat&oacute;rios</a></li>
                            <?php
                            session_start();
                            $tipo_usuario = $_SESSION['tipo_usuario'];

                            if ($tipo_usuario <> '3') {
                                ?>
                                <li style="width: 160px" id="menuCriaRelatorios"><a href="criaRelatorio.php">Criar Relat&oacute;rios</a></li>
                                <li style="width: 160px" id="menuUtilitarios" onclick="mostraMenu('utilitarios')"><a>Utilit&aacute;rios</a></li>
                                <li style="width: 160px"><a href="http://www.unimed-erechim.com.br">Sair</a></li>
                                <?php
                            } else {
                                ?>
                                <li style="width: 160px" >&nbsp;</li>
                                <li style="width: 160px" >&nbsp;</li>
                                <li style="width: 160px" align="right"><a href="http://www.unimed-erechim.com.br">Sair</a></li>
                                <?php
                            }
                            ?>
                        </ul>
                    </div>

                </div>
            </div>

            <div class="popover bottom" id="utilitarios" style="position: absolute; z-index: 999999; margin-left: 780px; margin-top: 200px ; width: 170px; display: none">
                <div class="arrow"></div>
                <ul style="height: 10px"></ul>
                <ul style="height: 30px" onclick="escondeMenu('utilitarios')"><a href="filtros.php">Filtros</a></ul>
                <ul style="height: 30px" onclick="escondeMenu('utilitarios')"><a href="extrato_beneficiario.php">Extrato Benefici&aacute;rio</a></ul>
                <ul style="height: 30px" onclick="escondeMenu('utilitarios')"><a href="usuarios.php">Usu&aacute;rios</a></ul>
            </div>
        </div>

