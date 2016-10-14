<?php

$procedimentos_grupo1 = "'40812022','40812030','40812049','40812057','40812065','40812073','30911044','30911052','30911060','30911079','30911087','30911095','30911109','30911117','30911125','30911133','30911141','30911150','30912016','30912024','30912032','30912040','30912083','30912091','30912105','31101240','31101259','31102310','31102328','31103103','31103111','40813908','40813924','20104243','20104251','20104260','20104278','20104286','20104294','20104316','40808190','40808203','40808211','40808289','40808297','40809064','40809072','40809080','40809102','40809153','40809161','40809170','40809188','40810020','40810046','40811018','40811026','40812014','40812022','40812030','40812049','40812057','40812065','40812073','40812081','40812090','40812103','40812111','40812120','40812138','30909023','30909031','30909139','30909147'";

set_time_limit(60000);

error_reporting(0);

ini_set("memory_limit", "-1");

include './conectorBD.php';
include './PHPExcel/PHPExcel.php';

$conexao = new conexao();
$conexao->dbase = 'unimed';
$conexao->conecta();

$objPHPExcel = new PHPExcel();

// Definimos o estilo da fonte
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);

// Criamos as colunas

$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', date('d/m/Y'));

$objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A2', 'Período - Faixa Etária')
        ->setCellValue('B2', "Número do Contrato")
        ->setCellValue("C2", "Nome da Empresa")
        ->setCellValue("D2", "Plano na ANS")
        ->setCellValue("E2", "Acomodação")
        ->setCellValue("F2", "Beneficiários")
        ->setCellValue("G2", "Mensalidade")
        ->setCellValue("H2", "Custo Assistencial")
        ->setCellValue("I2", "Custo Consulta")
        ->setCellValue("J2", "Custo Exames Grupo 1")
        ->setCellValue("K2", "Custo Exames Grupo 2")
        ->setCellValue("L2", "Custo Terapias Grupo 1")
        ->setCellValue("M2", "Custo Terapias Grupo 2")
        ->setCellValue("N2", "Custo Ambulatorial")
        ->setCellValue("O2", "Custo Internação")
        ->setCellValue("P2", "Custo Demais")
        ->setCellValue("Q2", "Custo Coparticipação")
        ->setCellValue("R2", "Eventos");

$planos = $_POST['planos'];
$contrato_ans = $_POST['contrato_ans'];
$grupo_planos = $_POST['grupo_planos'];
$grupo_pool = $_POST['grupo_pool'];
$renovacao_inicio = $_POST['renovacao_inicio'];
$renovacao_final = $_POST['renovacao_final'];
$data_inicio = $_POST['data_inicio'];
$data_final = $_POST['data_final'];
$competencia_inicial = date_format(date_create($data_inicio), 'Ym');
$competencia_final = date_format(date_create($data_final), 'Ym');
$where = null;
$linha = 3;

if (!empty($planos)) {
    $where = $where . ' AND UNI_PLANOS.CODIGOPLANO IN (' . $planos . ')';
}

if (!empty($contrato_ans)) {
    $where = $where . ' AND UNI_PLANOS.ID_CONTRATO_ANS = ' . $contrato_ans;
}

if (!empty($grupo_planos)) {
    $where = $where . ' AND UNI_PLANOS.ID_GRUPO_EMPRESA = ' . $grupo_planos;
}

if (!empty($grupo_pool)) {
    $where = $where . ' AND UNI_PLANOS.ID_GRUPO_POOL = ' . $grupo_pool;
}

if (!empty($renovacao_inicio)) {
    $where = $where . ' AND CAST(UNI_PLANOS.DATARENOVACAO AS DATE) >= "' . $renovacao_inicio . '"';
}

if (!empty($renovacao_final)) {
    $where = $where . ' AND CAST(UNI_PLANOS.DATARENOVACAO AS DATE) <= "' . $renovacao_final . '"';
}


$query = "select UNI_PLANOS.CODIGOPLANO, UNI_EMPRESAS.RAZAOSOCIAL, UNI_CONTRATO_ANS.REGISTRO CONTRATO,
	if(UNI_PLANOS.ACOMODACAO = 'P', 'Privativo',
		if(UNI_PLANOS.ACOMODACAO = 'S', 'Semiprivativo',
			if(UNI_PLANOS.ACOMODACAO = 'C', 'Coletivo',
				if(UNI_PLANOS.ACOMODACAO = 'A', 'Apartamento', 'Nenhuma (sem acomodação)')))) ACOMODACAO,
	UNI_PLANOS.IDPLANO
from unimed.UNI_PLANOS
	left join unimed.UNI_EMPRESAS on UNI_PLANOS.IDEMPRESA = UNI_EMPRESAS.IDEMPRESA
	left join unimed.UNI_CONTRATO_ANS on UNI_PLANOS.ID_CONTRATO_ANS = UNI_CONTRATO_ANS.ID
	left join unimed.UNI_BENEFICIARIOS on UNI_PLANOS.IDPLANO = UNI_BENEFICIARIOS.IDPLANO
where UNI_PLANOS.DATACANCELAMENTO is null " . $where . " group by UNI_PLANOS.IDPLANO";

$resultset = mysql_query($query);

$periodo = null;
$contrato = null;
$empresa = null;
$plano = null;
$acomodacao = null;
$beneficiarios = null;
$mensalidade = null;

$array_faixas = array(array(0, 18),
    array(19, 23),
    array(24, 28),
    array(29, 33),
    array(34, 38),
    array(39, 43),
    array(44, 48),
    array(49, 53),
    array(54, 58),
    array(59, 999));

while ($row = mysql_fetch_row($resultset)) {

    $contrato = ' ' . $row[0];
    $empresa = $row[1];
    $plano = $row[2];
    $acomodacao = $row[3];

    foreach ($array_faixas as $value) {

        $query_valores = "SELECT SUM(IF(FAT_PRODUCAO.TIPO_GUIA = 129, (FAT_PRODUCAO_SERVICOS_VALORES.CO_TAXA + FAT_PRODUCAO_SERVICOS_VALORES.CO_VALOR + FAT_PRODUCAO_SERVICOS_VALORES.FILME_TAXA + 
            FAT_PRODUCAO_SERVICOS_VALORES.FILME_VALOR + FAT_PRODUCAO_SERVICOS_VALORES.HM_TAXA + FAT_PRODUCAO_SERVICOS_VALORES.HM_VALOR),0)) CUSTOS_CONSULTA,
       SUM(IF(FAT_PRODUCAO.TIPO_GUIA = 130 AND FAT_SERVICOS.ID_SUB_TIPO = 22 
            AND (FAT_SERVICOS.CODIGO IN (" . $procedimentos_grupo1 . ") 
                    OR SUBSTRING(FAT_SERVICOS.CODIGO,1,4) = '4140' 
                    OR SUBSTRING(FAT_SERVICOS.CODIGO,1,4) = '4120' 
                    OR SUBSTRING(FAT_SERVICOS.CODIGO,1,4) = '4110' 
                    OR SUBSTRING(FAT_SERVICOS.CODIGO,1,4) = '4100'), 
                (FAT_PRODUCAO_SERVICOS_VALORES.CO_TAXA + FAT_PRODUCAO_SERVICOS_VALORES.CO_VALOR + FAT_PRODUCAO_SERVICOS_VALORES.FILME_TAXA + 
            FAT_PRODUCAO_SERVICOS_VALORES.FILME_VALOR + FAT_PRODUCAO_SERVICOS_VALORES.HM_TAXA + FAT_PRODUCAO_SERVICOS_VALORES.HM_VALOR),0)) CUSTO_TERAPIA_1,
       SUM(IF(FAT_PRODUCAO.TIPO_GUIA = 130 AND FAT_SERVICOS.ID_SUB_TIPO = 22
            AND (FAT_SERVICOS.CODIGO NOT IN (" . $procedimentos_grupo1 . ") 
                    OR SUBSTRING(FAT_SERVICOS.CODIGO,1,4) != '4140' 
                    OR SUBSTRING(FAT_SERVICOS.CODIGO,1,4) != '4120' 
                    OR SUBSTRING(FAT_SERVICOS.CODIGO,1,4) != '4110' 
                    OR SUBSTRING(FAT_SERVICOS.CODIGO,1,4) != '4100'), 
                    (FAT_PRODUCAO_SERVICOS_VALORES.CO_TAXA + FAT_PRODUCAO_SERVICOS_VALORES.CO_VALOR + FAT_PRODUCAO_SERVICOS_VALORES.FILME_TAXA + 
            FAT_PRODUCAO_SERVICOS_VALORES.FILME_VALOR + FAT_PRODUCAO_SERVICOS_VALORES.HM_TAXA + FAT_PRODUCAO_SERVICOS_VALORES.HM_VALOR),0)) CUSTO_TERAPIA_2,
       SUM(IF(FAT_PRODUCAO.TIPO_GUIA = 130 AND FAT_SERVICOS.ID_SUB_TIPO <> 22        
            AND (FAT_SERVICOS.CODIGO IN (" . $procedimentos_grupo1 . ") 
                    OR SUBSTRING(FAT_SERVICOS.CODIGO,1,4) = '4140' 
                    OR SUBSTRING(FAT_SERVICOS.CODIGO,1,4) = '4120' 
                    OR SUBSTRING(FAT_SERVICOS.CODIGO,1,4) = '4110' 
                    OR SUBSTRING(FAT_SERVICOS.CODIGO,1,4) = '4100'),  
                    (FAT_PRODUCAO_SERVICOS_VALORES.CO_TAXA + FAT_PRODUCAO_SERVICOS_VALORES.CO_VALOR + FAT_PRODUCAO_SERVICOS_VALORES.FILME_TAXA + 
            FAT_PRODUCAO_SERVICOS_VALORES.FILME_VALOR + FAT_PRODUCAO_SERVICOS_VALORES.HM_TAXA + FAT_PRODUCAO_SERVICOS_VALORES.HM_VALOR),0)) CUSTO_EXAMES_1,
       SUM(IF(FAT_PRODUCAO.TIPO_GUIA = 130 AND FAT_SERVICOS.ID_SUB_TIPO <> 22        
            AND (FAT_SERVICOS.CODIGO NOT IN (" . $procedimentos_grupo1 . ") 
                    OR SUBSTRING(FAT_SERVICOS.CODIGO,1,4) != '4140' 
                    OR SUBSTRING(FAT_SERVICOS.CODIGO,1,4) != '4120' 
                    OR SUBSTRING(FAT_SERVICOS.CODIGO,1,4) != '4110' 
                    OR SUBSTRING(FAT_SERVICOS.CODIGO,1,4) != '4100'),  
                    (FAT_PRODUCAO_SERVICOS_VALORES.CO_TAXA + FAT_PRODUCAO_SERVICOS_VALORES.CO_VALOR + FAT_PRODUCAO_SERVICOS_VALORES.FILME_TAXA + 
            FAT_PRODUCAO_SERVICOS_VALORES.FILME_VALOR + FAT_PRODUCAO_SERVICOS_VALORES.HM_TAXA + FAT_PRODUCAO_SERVICOS_VALORES.HM_VALOR),0)) CUSTO_EXAMES_2,
       SUM(IF(FAT_PRODUCAO.TIPO_GUIA = 131 AND FAT_PRODUCAO_AUX.NUMERO_DIARIAS = 0, (FAT_PRODUCAO_SERVICOS_VALORES.CO_TAXA + FAT_PRODUCAO_SERVICOS_VALORES.CO_VALOR + FAT_PRODUCAO_SERVICOS_VALORES.FILME_TAXA + 
            FAT_PRODUCAO_SERVICOS_VALORES.FILME_VALOR + FAT_PRODUCAO_SERVICOS_VALORES.HM_TAXA + FAT_PRODUCAO_SERVICOS_VALORES.HM_VALOR),0)) CUSTO_AMBULATORIAL,
       SUM(IF(FAT_PRODUCAO.TIPO_GUIA = 131 AND FAT_PRODUCAO_AUX.NUMERO_DIARIAS <> 0, (FAT_PRODUCAO_SERVICOS_VALORES.CO_TAXA + FAT_PRODUCAO_SERVICOS_VALORES.CO_VALOR + FAT_PRODUCAO_SERVICOS_VALORES.FILME_TAXA + 
            FAT_PRODUCAO_SERVICOS_VALORES.FILME_VALOR + FAT_PRODUCAO_SERVICOS_VALORES.HM_TAXA + FAT_PRODUCAO_SERVICOS_VALORES.HM_VALOR),0)) CUSTO_INTERNACAO,
       SUM(IF(FAT_PRODUCAO.TIPO_GUIA = 132, (FAT_PRODUCAO_SERVICOS_VALORES.CO_TAXA + FAT_PRODUCAO_SERVICOS_VALORES.CO_VALOR + FAT_PRODUCAO_SERVICOS_VALORES.FILME_TAXA + 
            FAT_PRODUCAO_SERVICOS_VALORES.FILME_VALOR + FAT_PRODUCAO_SERVICOS_VALORES.HM_TAXA + FAT_PRODUCAO_SERVICOS_VALORES.HM_VALOR),0)) CUSTOS_DEMAIS,
            FAT_LOTES.COMPETENCIA,
            COUNT(*) EVENTOS
        from FAT_PRODUCAO
            inner join FAT_PRODUCAO_SERVICOS on FAT_PRODUCAO_SERVICOS.ID_PRODUCAO = FAT_PRODUCAO.ID
            inner join FAT_SERVICOS on FAT_PRODUCAO_SERVICOS.ID_SERVICO = FAT_SERVICOS.ID
            inner join FAT_PRODUCAO_SERVICOS_VALORES on FAT_PRODUCAO_SERVICOS.ID = FAT_PRODUCAO_SERVICOS_VALORES.ID_PRODUCAO_SERVICO AND FAT_PRODUCAO_SERVICOS_VALORES.ID_TIPO = 252
            inner join FAT_PRODUCAO_AUX on FAT_PRODUCAO_AUX.ID_PRODUCAO = FAT_PRODUCAO.ID
            inner join FAT_LOTES_PRESTADORES on FAT_PRODUCAO.ID_LOTE_PRESTADOR = FAT_LOTES_PRESTADORES.ID_LOTE_PRESTADOR
            inner join FAT_LOTES on FAT_LOTES_PRESTADORES.ID_LOTE = FAT_LOTES.ID_LOTE
            inner join UNI_BENEFICIARIOS on FAT_PRODUCAO.ID_BENEFICIARIO = UNI_BENEFICIARIOS.IDBENEFICIARIO
            inner join UNI_PESSOAS on UNI_BENEFICIARIOS.IDPESSOA = UNI_PESSOAS.IDPESSOA
        where UNI_BENEFICIARIOS.IDPLANO = " . $row[4] . "
            and FAT_LOTES.COMPETENCIA >= '" . $competencia_inicial . "' and FAT_LOTES.COMPETENCIA <= '" . $competencia_final . "'
            and TIMESTAMPDIFF(YEAR,UNI_PESSOAS.DATANASC,CURRENT_DATE) > " . $value[0] . "
            and TIMESTAMPDIFF(YEAR,UNI_PESSOAS.DATANASC,CURRENT_DATE) <= " . $value[1] . "
        group by FAT_LOTES.COMPETENCIA";

        $resultset_valores = mysql_query($query_valores);

        $custo_assistencial = null;
        $custo_consulta = null;
        $custo_exames_1 = null;
        $custo_exames_2 = null;
        $custo_terapias_1 = null;
        $custo_terapias_2 = null;
        $custo_ambulatorial = null;
        $custo_internacao = null;
        $custo_demais = null;
        $custo_coparticipacao = null;

        while ($row_valores = mysql_fetch_row($resultset_valores)) {

            $mes = substr($row_valores[8], 4, 2);      // Mês desejado, pode ser por ser obtido por POST, GET, etc.
            $ano = substr($row_valores[8], 0, 4); // Ano atual
            $data_competencia = $ano . '-' . $mes . '-' . date("t", mktime(0, 0, 0, $mes, '01', $ano)); // Mágica, plim!

            $custo_consulta = $row_valores[0];
            $custo_terapias_1 = $row_valores[1];
            $custo_terapias_2 = $row_valores[2];
            $custo_exames_1 = $row_valores[3];
            $custo_exames_2 = $row_valores[4];
            $custo_ambulatorial = $row_valores[5];
            $custo_internacao = $row_valores[6];
            $custo_demais = $row_valores[7];

            $periodo = $row_valores[8] . ' - De ' . $value[0] . ' até ' . $value[1] . ' anos ';

            $query_coparticipacao = 'select SUM(FAT_PRODUCAO_AUX.VALOR_COPARTICIPACAO + FAT_PRODUCAO_AUX.VALOR_ADIANTAMENTO) COPARTICIPACAO 
            from FAT_PRODUCAO_AUX 
            inner join FAT_PRODUCAO on FAT_PRODUCAO_AUX.ID_PRODUCAO = FAT_PRODUCAO.ID
            inner join FAT_LOTES_PRESTADORES on FAT_PRODUCAO.ID_LOTE_PRESTADOR = FAT_LOTES_PRESTADORES.ID_LOTE_PRESTADOR
            inner join FAT_LOTES on FAT_LOTES_PRESTADORES.ID_LOTE = FAT_LOTES.ID_LOTE
            inner join UNI_BENEFICIARIOS on FAT_PRODUCAO.ID_BENEFICIARIO = UNI_BENEFICIARIOS.IDBENEFICIARIO
            inner join UNI_PESSOAS on UNI_BENEFICIARIOS.IDPESSOA = UNI_PESSOAS.IDPESSOA
            where UNI_BENEFICIARIOS.IDPLANO = ' . $row[4] . ' 
                and TIMESTAMPDIFF(YEAR,UNI_PESSOAS.DATANASC,"' . $data_competencia . '") > ' . $value[0] . '
                and TIMESTAMPDIFF(YEAR,UNI_PESSOAS.DATANASC,"' . $data_competencia . '") <= ' . $value[1] . '
                AND FAT_LOTES.COMPETENCIA = "' . $row_valores[8] . '"';

            $resultset_coparticipacao = mysql_query($query_coparticipacao);
            $row_coparticipacao = mysql_fetch_row($resultset_coparticipacao);

            $custo_coparticipacao = $row_coparticipacao[0];

            $custo_assistencial = ($row_valores[0] + $row_valores[1] + $row_valores[2] + $row_valores[3] + $row_valores[4] + $row_valores[5] + $row_valores[6] + $row_valores[7]) - $custo_coparticipacao;

            $query_beneficiarios = "select count(*) BENEFICIARIOS from unimed.UNI_BENEFICIARIOS BENEF
                inner join unimed.UNI_PESSOAS on BENEF.IDPESSOA = UNI_PESSOAS.IDPESSOA
		where BENEF.IDPLANO = " . $row[4] . "
                and TIMESTAMPDIFF(YEAR,UNI_PESSOAS.DATANASC,'" . $data_competencia . "') > " . $value[0] . "
                and TIMESTAMPDIFF(YEAR,UNI_PESSOAS.DATANASC,'" . $data_competencia . "') <= " . $value[1] . "
                and DATE_FORMAT(BENEF.DATAINCLUSAO,'%Y%m') <= '" . $row_valores[8] . "'
                and (DATE_FORMAT(BENEF.DATACANCELAMENTO,'%Y%m') > '" . $row_valores[8] . "' or BENEF.DATACANCELAMENTO is null)";

            $resultset_beneficiarios = mysql_query($query_beneficiarios);
            $row_beneficiarios = mysql_fetch_row($resultset_beneficiarios);

            $beneficiarios = $row_beneficiarios[0];


            $query_mensalidade = "SELECT DATE_FORMAT(CAST(mens.DT_EMSS AS DATE),'%Y%m') PERIODO, CONCAT(mens.CD_EMPR,mens.CD_USUA)
                FROM bitmed.BT008 mens 
                        LEFT JOIN unimed.UNI_PLANOS planos ON planos.CODIGOPLANO = mens.CD_EMPR
                WHERE DATE_FORMAT(CAST(mens.DT_EMSS AS DATE),'%Y%m') = '" . $row_valores[8] . "'
                AND planos.IDPLANO = " . $row[4];
            $resultset_mensalidade = mysql_query($query_mensalidade);
            $valor_mensalidade = 0;
            while ($row_mensalidade = mysql_fetch_row($resultset_mensalidade)) {
                $query_valor_mensalidade = "SELECT (select mensvalor.VALOR from bitmed.BT052 mensvalor
                where UNI_BENEFICIARIOS.CODUSUARIO = CONCAT(mensvalor.CD_USUA, FN_CALC_DIGITO_UNIMED(mensvalor.CD_USUA))
                and DATE_FORMAT(CAST(mensvalor.DATA AS DATE),'%Y%m') <= '".$row_mensalidade[0]."'
                order by mensvalor.DATA desc limit 1) VALOR,
                UNI_BENEFICIARIOS.CODUSUARIO from UNI_BENEFICIARIOS  
                LEFT JOIN unimed.UNI_PESSOAS on UNI_PESSOAS.IDPESSOA = UNI_BENEFICIARIOS.IDPESSOA 
                where SUBSTR(UNI_BENEFICIARIOS.CODUSUARIO,1,14) = '".$row_mensalidade[1]."' 
                and TIMESTAMPDIFF(YEAR,UNI_PESSOAS.DATANASC,'" . $data_competencia . "') > " . $value[0] . " 
                and TIMESTAMPDIFF(YEAR,UNI_PESSOAS.DATANASC,'" . $data_competencia . "') <= " . $value[1] . "
                group by UNI_BENEFICIARIOS.CODUSUARIO";
                $resultset_valor_mensalidade = mysql_query($query_valor_mensalidade);
                while($row_valor_mensalidade = mysql_fetch_row($resultset_valor_mensalidade)){
                    $valor_mensalidade = $valor_mensalidade + $row_valor_mensalidade[0];
                }                
            }

            $mensalidade = $valor_mensalidade;

            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $linha, $periodo);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $contrato);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $empresa);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $plano);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $acomodacao);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $beneficiarios);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $mensalidade);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $custo_assistencial);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $custo_consulta);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, $custo_exames_1);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(10, $linha, $custo_exames_2);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(11, $linha, $custo_terapias_1);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(12, $linha, $custo_terapias_2);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(13, $linha, $custo_ambulatorial);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(14, $linha, $custo_internacao);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(15, $linha, $custo_demais);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(16, $linha, $custo_coparticipacao);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(17, $linha, $row_valores[9]);

            $linha++;
        }
    }
}

//$objPHPExcel->getActiveSheet()->setTitle($titulo);
// Cabeçalho do arquivo para ele baixar
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="rdc28.xls"');
header('Content-Transfer-Encoding: binary');
header('Cache-Control: max-age=0');
// Acessamos o 'Writer' para poder salvar o arquivo
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

// Salva diretamente no output, poderíamos mudar arqui para um nome de arquivo em um diretório ,caso não quisessemos jogar na tela
$objWriter->save('php://output');

echo '<script>alert("Arquivo gerado com Sucesso!")</script>';
echo '<script type="text/javascript">location.href = "rdc28.php";</script>';
