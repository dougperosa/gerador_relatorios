<?php

set_time_limit(600);

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
        ->setCellValue('A2', 'Período')
        ->setCellValue('B2', "Número do Contrato")
        ->setCellValue("C2", "Nome da Empresa")
        ->setCellValue("D2", "Plano na ANS")
        ->setCellValue("E2", "Acomodação")
        ->setCellValue("F2", "Beneficiários")
        ->setCellValue("G2", "Mensalidade")
        ->setCellValue("H2", "Custo Assistencial")
        ->setCellValue("I2", "Custo Consulta")
        ->setCellValue("J2", "Custo Exames")
        ->setCellValue("K2", "Custo Terapias")
        ->setCellValue("L2", "Custo Ambulatorial")
        ->setCellValue("M2", "Custo Internação")
        ->setCellValue("N2", "Custo Demais")
        ->setCellValue("O2", "Custo Coparticipação")
        ->setCellValue("P2", "Índice de atualização Monetária")
        ->setCellValue("Q2", "Sinistralidade Meta");

$planos = $_POST['planos'];
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
	inner join unimed.UNI_EMPRESAS on UNI_PLANOS.IDEMPRESA = UNI_EMPRESAS.IDEMPRESA
	left join unimed.UNI_CONTRATO_ANS on UNI_PLANOS.ID_CONTRATO_ANS = UNI_CONTRATO_ANS.ID
	inner join unimed.UNI_BENEFICIARIOS on UNI_PLANOS.IDPLANO = UNI_BENEFICIARIOS.IDPLANO
where UNI_PLANOS.DATACANCELAMENTO is null " . $where . " group by UNI_PLANOS.IDPLANO";

$resultset = mysql_query($query);

$periodo = null;
$contrato = null;
$empresa = null;
$plano = null;
$acomodacao = null;
$beneficiarios = null;
$mensalidade = null;

while ($row = mysql_fetch_row($resultset)) {

    $contrato = ' ' . $row[0];
    $empresa = $row[1];
    $plano = $row[2];
    $acomodacao = $row[3];

    $query_valores = "SELECT SUM(IF(FAT_PRODUCAO.TIPO_GUIA = 129, (FAT_PRODUCAO_SERVICOS_VALORES.CO_TAXA + FAT_PRODUCAO_SERVICOS_VALORES.CO_VALOR + FAT_PRODUCAO_SERVICOS_VALORES.FILME_TAXA + 
            FAT_PRODUCAO_SERVICOS_VALORES.FILME_VALOR + FAT_PRODUCAO_SERVICOS_VALORES.HM_TAXA + FAT_PRODUCAO_SERVICOS_VALORES.HM_VALOR),0)) CUSTOS_CONSULTA,
       SUM(IF(FAT_PRODUCAO.TIPO_GUIA = 130 AND FAT_SERVICOS.ID_SUB_TIPO = 22, (FAT_PRODUCAO_SERVICOS_VALORES.CO_TAXA + FAT_PRODUCAO_SERVICOS_VALORES.CO_VALOR + FAT_PRODUCAO_SERVICOS_VALORES.FILME_TAXA + 
            FAT_PRODUCAO_SERVICOS_VALORES.FILME_VALOR + FAT_PRODUCAO_SERVICOS_VALORES.HM_TAXA + FAT_PRODUCAO_SERVICOS_VALORES.HM_VALOR),0)) CUSTO_TERAPIA,
       SUM(IF(FAT_PRODUCAO.TIPO_GUIA = 130 AND FAT_SERVICOS.ID_SUB_TIPO <> 22, (FAT_PRODUCAO_SERVICOS_VALORES.CO_TAXA + FAT_PRODUCAO_SERVICOS_VALORES.CO_VALOR + FAT_PRODUCAO_SERVICOS_VALORES.FILME_TAXA + 
            FAT_PRODUCAO_SERVICOS_VALORES.FILME_VALOR + FAT_PRODUCAO_SERVICOS_VALORES.HM_TAXA + FAT_PRODUCAO_SERVICOS_VALORES.HM_VALOR),0)) CUSTO_EXAMES,
       SUM(IF(FAT_PRODUCAO.TIPO_GUIA = 131 AND FAT_PRODUCAO_AUX.NUMERO_DIARIAS = 0, (FAT_PRODUCAO_SERVICOS_VALORES.CO_TAXA + FAT_PRODUCAO_SERVICOS_VALORES.CO_VALOR + FAT_PRODUCAO_SERVICOS_VALORES.FILME_TAXA + 
            FAT_PRODUCAO_SERVICOS_VALORES.FILME_VALOR + FAT_PRODUCAO_SERVICOS_VALORES.HM_TAXA + FAT_PRODUCAO_SERVICOS_VALORES.HM_VALOR),0)) CUSTO_AMBULATORIAL,
       SUM(IF(FAT_PRODUCAO.TIPO_GUIA = 131 AND FAT_PRODUCAO_AUX.NUMERO_DIARIAS <> 0, (FAT_PRODUCAO_SERVICOS_VALORES.CO_TAXA + FAT_PRODUCAO_SERVICOS_VALORES.CO_VALOR + FAT_PRODUCAO_SERVICOS_VALORES.FILME_TAXA + 
            FAT_PRODUCAO_SERVICOS_VALORES.FILME_VALOR + FAT_PRODUCAO_SERVICOS_VALORES.HM_TAXA + FAT_PRODUCAO_SERVICOS_VALORES.HM_VALOR),0)) CUSTO_INTERNACAO,
       SUM(IF(FAT_PRODUCAO.TIPO_GUIA = 132, (FAT_PRODUCAO_SERVICOS_VALORES.CO_TAXA + FAT_PRODUCAO_SERVICOS_VALORES.CO_VALOR + FAT_PRODUCAO_SERVICOS_VALORES.FILME_TAXA + 
            FAT_PRODUCAO_SERVICOS_VALORES.FILME_VALOR + FAT_PRODUCAO_SERVICOS_VALORES.HM_TAXA + FAT_PRODUCAO_SERVICOS_VALORES.HM_VALOR),0)) CUSTOS_DEMAIS,
            FAT_LOTES.COMPETENCIA
        from FAT_PRODUCAO
            inner join FAT_PRODUCAO_SERVICOS on FAT_PRODUCAO_SERVICOS.ID_PRODUCAO = FAT_PRODUCAO.ID
            inner join FAT_SERVICOS on FAT_PRODUCAO_SERVICOS.ID_SERVICO = FAT_SERVICOS.ID
            inner join FAT_PRODUCAO_SERVICOS_VALORES on FAT_PRODUCAO_SERVICOS.ID = FAT_PRODUCAO_SERVICOS_VALORES.ID_PRODUCAO_SERVICO AND FAT_PRODUCAO_SERVICOS_VALORES.ID_TIPO = 252
            inner join FAT_PRODUCAO_AUX on FAT_PRODUCAO_AUX.ID_PRODUCAO = FAT_PRODUCAO.ID
            inner join FAT_LOTES_PRESTADORES on FAT_PRODUCAO.ID_LOTE_PRESTADOR = FAT_LOTES_PRESTADORES.ID_LOTE_PRESTADOR
            inner join FAT_LOTES on FAT_LOTES_PRESTADORES.ID_LOTE = FAT_LOTES.ID_LOTE
            inner join UNI_BENEFICIARIOS on FAT_PRODUCAO.ID_BENEFICIARIO = UNI_BENEFICIARIOS.IDBENEFICIARIO
        where UNI_BENEFICIARIOS.IDPLANO = " . $row[4] . "
            and FAT_LOTES.COMPETENCIA >= '" . $competencia_inicial . "' and FAT_LOTES.COMPETENCIA <= '" . $competencia_final . "'
        group by FAT_LOTES.COMPETENCIA";

    $resultset_valores = mysql_query($query_valores);

    $custo_assistencial = null;
    $custo_consulta = null;
    $custo_exames = null;
    $custo_terapias = null;
    $custo_ambulatorial = null;
    $custo_internacao = null;
    $custo_demais = null;
    $custo_coparticipacao = null;

    while ($row_valores = mysql_fetch_row($resultset_valores)) {

        $custo_consulta = $row_valores[0];
        $custo_terapias = $row_valores[1];
        $custo_exames = $row_valores[2];
        $custo_ambulatorial = $row_valores[3];
        $custo_internacao = $row_valores[4];
        $custo_demais = $row_valores[5];

        $periodo = $row_valores[6];

        $query_coparticipacao = 'select SUM(FAT_PRODUCAO_AUX.VALOR_COPARTICIPACAO + FAT_PRODUCAO_AUX.VALOR_ADIANTAMENTO) COPARTICIPACAO 
            from FAT_PRODUCAO_AUX 
            inner join FAT_PRODUCAO on FAT_PRODUCAO_AUX.ID_PRODUCAO = FAT_PRODUCAO.ID
            inner join FAT_LOTES_PRESTADORES on FAT_PRODUCAO.ID_LOTE_PRESTADOR = FAT_LOTES_PRESTADORES.ID_LOTE_PRESTADOR
            inner join FAT_LOTES on FAT_LOTES_PRESTADORES.ID_LOTE = FAT_LOTES.ID_LOTE
            inner join UNI_BENEFICIARIOS on FAT_PRODUCAO.ID_BENEFICIARIO = UNI_BENEFICIARIOS.IDBENEFICIARIO
            where UNI_BENEFICIARIOS.IDPLANO = '.$row[4].' AND FAT_LOTES.COMPETENCIA = "' . $periodo . '"';
        $resultset_coparticipacao = mysql_query($query_coparticipacao);
        $row_coparticipacao = mysql_fetch_row($resultset_coparticipacao);

        $custo_coparticipacao = $row_coparticipacao[0];

        $custo_assistencial = ($row_valores[0] + $row_valores[1] + $row_valores[2] + $row_valores[3] + $row_valores[4] + $row_valores[5]) - $custo_coparticipacao;

        $query_beneficiarios = "select count(*) BENEFICIARIOS from unimed.UNI_BENEFICIARIOS BENEF 
		where BENEF.IDPLANO = " . $row[4] . "
                and DATE_FORMAT(BENEF.DATAINCLUSAO,'%Y%m') <= '" . $periodo . "'
                and (DATE_FORMAT(BENEF.DATACANCELAMENTO,'%Y%m') > '" . $periodo . "' or BENEF.DATACANCELAMENTO is null)";

        $resultset_beneficiarios = mysql_query($query_beneficiarios);
        $row_beneficiarios = mysql_fetch_row($resultset_beneficiarios);

        $beneficiarios = $row_beneficiarios[0];

        $query_mensalidade = "SELECT DISTINCT(PERIODO), SUM(VALOR) AS VALOR FROM
            (SELECT DATE_FORMAT(CAST(mens.DT_EMSS AS DATE),'%Y%m') PERIODO, ROUND(SUM(mens.VL_MENS),2) AS VALOR
            FROM bitmed.BT008 mens
                    LEFT JOIN unimed.UNI_PLANOS planos ON planos.CODIGOPLANO = mens.CD_EMPR
            WHERE DATE_FORMAT(CAST(mens.DT_EMSS AS DATE),'%Y%m') = '" . $periodo . "'
            AND mens.ST_PAGT <> 'C'
            AND planos.IDPLANO = " . $row[4] . " 
            UNION ALL
            SELECT DATE_FORMAT(CAST(fat.DT_EMSS AS DATE),'%Y%m') PERIODO, ROUND(SUM(ifat.VL_TOTAL),2) AS VALOR
            FROM bitmed.BT013 fat
                    LEFT JOIN bitmed.BT014 ifat ON ifat.CD_DUPL = fat.CD_DUPL
                    LEFT JOIN unimed.UNI_PLANOS planos ON planos.CODIGOPLANO = fat.CD_EMPR
            WHERE DATE_FORMAT(CAST(fat.DT_EMSS AS DATE),'%Y%m') = '" . $periodo . "'
            AND ifat.CD_ITEM IN (101,103,107,109,204,205,206,207,208,120,151,157,108,110,121,158,320,321,322,323,324,325,326)
            AND planos.IDPLANO = " . $row[4] . ") MENSALIDADE GROUP BY 1 HAVING (PERIODO)";

        $resultset_mensalidade = mysql_query($query_mensalidade);
        $row_mensalidade = mysql_fetch_row($resultset_mensalidade);

        $mensalidade = $row_mensalidade[1];

        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $linha, $periodo);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $contrato);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, $empresa);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $plano);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $acomodacao);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $linha, $beneficiarios);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $linha, $mensalidade);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $linha, $custo_assistencial);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8, $linha, $custo_consulta);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(9, $linha, $custo_exames);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(10, $linha, $custo_terapias);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(11, $linha, $custo_ambulatorial);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(12, $linha, $custo_internacao);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(13, $linha, $custo_demais);
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(14, $linha, $custo_coparticipacao);

        $linha++;
    }
}

//$objPHPExcel->getActiveSheet()->setTitle($titulo);
// Cabeçalho do arquivo para ele baixar
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="estatistico_planos.xls"');
header('Content-Transfer-Encoding: binary');
header('Cache-Control: max-age=0');
// Acessamos o 'Writer' para poder salvar o arquivo
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

// Salva diretamente no output, poderíamos mudar arqui para um nome de arquivo em um diretório ,caso não quisessemos jogar na tela
$objWriter->save('php://output');

echo '<script>alert("Arquivo gerado com Sucesso!")</script>';
echo '<script type="text/javascript">location.href = "estatistico_planos.php";</script>';
