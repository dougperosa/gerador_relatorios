<?php

set_time_limit(600);

error_reporting(0);

include './funcoes.php';
include './conectorBD.php';

$conexao = new conexao();
$conexao->conecta();

/* Função fopen usada para abrir arquivo, ou seja, joga-lo na memória do servidor, neste caso o arquivo ainda não existe.
  o “w” quer dizer write, que o arquivo pode ser escrito */

$cod_unimed = '0028';
$competencia = $_POST['competencia'];

$nome_arquivo = 'EB' . $competencia . '.028';

$arquivo = fopen($nome_arquivo, 'w');

$header = '00000001101' . $cod_unimed . date('Y') . date('m') . date('d') . $competencia . '010203' . PHP_EOL;

fwrite($arquivo, $header);

$beneficiario = null;
$prestador = null;
$procedimentos = null;
$trailer = null;
$quant_beneficiario = null;
$quant_prestador = null;
$quant_procedimentos = null;

$sequencia = 1;

$query_pessoas = 'select UNI_PESSOAS.NOME, YEAR(UNI_PESSOAS.DATANASC) ANO_NASC, MONTH(UNI_PESSOAS.DATANASC) MES_NASC, DAY(UNI_PESSOAS.DATANASC) DIA_NASC, 
	IF(UNI_PLANOS.ESPECIE <> "1",
		UNI_EMPRESAS.CNPJ,
		"") CNPJ_EMPRESA,
	SUBSTRING(UNI_BENEFICIARIOS.CODUSUARIO,5,17) COD_BENEFICIARIO, UNI_BENEFICIARIOS.DEPENDENCIA, 
	SUBSTRING(B.CODUSUARIO,5,17) COD_TITULAR, P.NOME, UNI_PESSOAS.CNS, UNI_CONTRATO_ANS.REGISTRO, UNI_PLANOS.PLANO	
from FAT_PRODUCAO
	left join FAT_LOTES_PRESTADORES on FAT_PRODUCAO.ID_LOTE_PRESTADOR = FAT_LOTES_PRESTADORES.ID_LOTE_PRESTADOR
	left join FAT_LOTES on FAT_LOTES_PRESTADORES.ID_LOTE = FAT_LOTES.ID_LOTE
	left join UNI_BENEFICIARIOS on FAT_PRODUCAO.ID_BENEFICIARIO = UNI_BENEFICIARIOS.IDBENEFICIARIO
	left join UNI_PLANOS on UNI_BENEFICIARIOS.IDPLANO = UNI_PLANOS.IDPLANO
	left join UNI_CONTRATO_ANS on UNI_PLANOS.ID_CONTRATO_ANS = UNI_CONTRATO_ANS.ID
	left join UNI_EMPRESAS on UNI_PLANOS.IDEMPRESA = UNI_EMPRESAS.IDEMPRESA
	left join UNI_PESSOAS on UNI_BENEFICIARIOS.IDPESSOA = UNI_PESSOAS.IDPESSOA
	left join UNI_BENEFICIARIOS B on B.IDBENEFICIARIO = UNI_BENEFICIARIOS.IDTITULAR
	left join UNI_PESSOAS P on P.IDPESSOA = B.IDPESSOA
where FAT_LOTES.COMPETENCIA = "' . $competencia . '"
and FAT_PRODUCAO.ID_BENEFICIARIO IS NOT NULL
and UNI_PLANOS.TIPOPLANO = 1
and FAT_PRODUCAO.PEA <> "S"
GROUP BY 1';
$resultset_pessoas = mysql_query($query_pessoas);
while ($row_pessoas = mysql_fetch_row($resultset_pessoas)) {
    $sequencia++;
    $quant_beneficiario++;

    $query_extrato = 'select IF(FAT_PRODUCAO.TIPO_GUIA = 129,(FAT_PRODUCAO_SERVICOS_VALORES.CO_TAXA) + (FAT_PRODUCAO_SERVICOS_VALORES.CO_VALOR) + (FAT_PRODUCAO_SERVICOS_VALORES.FILME_TAXA) +
    (FAT_PRODUCAO_SERVICOS_VALORES.FILME_VALOR) + (FAT_PRODUCAO_SERVICOS_VALORES.HM_TAXA) + (FAT_PRODUCAO_SERVICOS_VALORES.HM_VALOR), 0) VALOR_CONSULTAS,
	IF(FAT_PRODUCAO.TIPO_GUIA = 130 AND SUBSTRING(FAT_PRODUCAO.NUMERO_DOCUMENTO,1,1) = "E",(FAT_PRODUCAO_SERVICOS_VALORES.CO_TAXA) + (FAT_PRODUCAO_SERVICOS_VALORES.CO_VALOR) + (FAT_PRODUCAO_SERVICOS_VALORES.FILME_TAXA) +
    (FAT_PRODUCAO_SERVICOS_VALORES.FILME_VALOR) + (FAT_PRODUCAO_SERVICOS_VALORES.HM_TAXA) + (FAT_PRODUCAO_SERVICOS_VALORES.HM_VALOR), 0) VALOR_EXAMES,
	IF(FAT_PRODUCAO.TIPO_GUIA = 131,(FAT_PRODUCAO_SERVICOS_VALORES.CO_TAXA) + (FAT_PRODUCAO_SERVICOS_VALORES.CO_VALOR) + (FAT_PRODUCAO_SERVICOS_VALORES.FILME_TAXA) +
    (FAT_PRODUCAO_SERVICOS_VALORES.FILME_VALOR) + (FAT_PRODUCAO_SERVICOS_VALORES.HM_TAXA) + (FAT_PRODUCAO_SERVICOS_VALORES.HM_VALOR), 0) VALOR_INTERNACAO,
	IF((FAT_PRODUCAO.TIPO_GUIA = 130 AND SUBSTRING(FAT_PRODUCAO.NUMERO_DOCUMENTO,1,1) <> "E") OR (FAT_PRODUCAO.TIPO_GUIA = 132),(FAT_PRODUCAO_SERVICOS_VALORES.CO_TAXA) + (FAT_PRODUCAO_SERVICOS_VALORES.CO_VALOR) + (FAT_PRODUCAO_SERVICOS_VALORES.FILME_TAXA) +
    (FAT_PRODUCAO_SERVICOS_VALORES.FILME_VALOR) + (FAT_PRODUCAO_SERVICOS_VALORES.HM_TAXA) + (FAT_PRODUCAO_SERVICOS_VALORES.HM_VALOR), 0) VALOR_OUTROS
    from FAT_PRODUCAO
	left join FAT_LOTES_PRESTADORES on FAT_PRODUCAO.ID_LOTE_PRESTADOR = FAT_LOTES_PRESTADORES.ID_LOTE_PRESTADOR
	left join FAT_LOTES on FAT_LOTES_PRESTADORES.ID_LOTE = FAT_LOTES.ID_LOTE
	left join UNI_BENEFICIARIOS on FAT_PRODUCAO.ID_BENEFICIARIO = UNI_BENEFICIARIOS.IDBENEFICIARIO
	left join FAT_PRODUCAO_AUX on FAT_PRODUCAO.ID = FAT_PRODUCAO_AUX.ID_PRODUCAO
	left join FAT_PRODUCAO_SERVICOS on FAT_PRODUCAO.ID = FAT_PRODUCAO_SERVICOS.ID_PRODUCAO
	left join FAT_PRODUCAO_SERVICOS_VALORES on FAT_PRODUCAO_SERVICOS.ID = FAT_PRODUCAO_SERVICOS_VALORES.ID_PRODUCAO_SERVICO and ID_TIPO = 252
    where FAT_LOTES.COMPETENCIA = "' . $competencia . '"
    and UNI_BENEFICIARIOS.CODUSUARIO = "0028' . $row_pessoas[5] . '" and FAT_PRODUCAO.PEA <> "S"';
    $resultset_extrato = mysql_query($query_extrato);

    $valor_consulta = null;
    $valor_exames = null;
    $valor_internacao = null;
    $valor_outros = null;
    $valor_odonto = null;

    while ($row_extrato = mysql_fetch_row($resultset_extrato)) {
        $valor_consulta += $row_extrato[0];
        $valor_exames += $row_extrato[1];
        $valor_internacao += $row_extrato[2];
        $valor_outros += $row_extrato[3];
    }


    $beneficiario = substr(str_pad($sequencia, 8, "0", STR_PAD_LEFT), 0, 8) . '103' . substr(str_pad($row_pessoas[0], 70, " "), 0, 70) . substr(str_pad($row_pessoas[1], 4, "0", STR_PAD_LEFT), 0, 4) .
            substr(str_pad($row_pessoas[2], 2, "0", STR_PAD_LEFT), 0, 2) . substr(str_pad($row_pessoas[3], 2, "0", STR_PAD_LEFT), 0, 2) . '0028' . substr(str_pad($row_pessoas[4], 15, " "), 0, 15) .
            substr(str_pad($row_pessoas[5], 13, " "), 0, 13) . substr(str_pad($row_pessoas[6], 2, " "), 0, 6) . substr(str_pad($row_pessoas[7], 13, " "), 0, 13) . substr(str_pad($row_pessoas[8], 70, " "), 0, 70) .
            substr(str_pad($row_pessoas[9], 15, " ", STR_PAD_LEFT), 0, 15) . substr(str_pad($row_pessoas[10], 20, " ", STR_PAD_LEFT), 0, 20) . substr(str_pad($row_pessoas[11], 60, " "), 0, 60) .
            str_pad(" ", 40, " ") . substr(str_pad(str_replace(".", "", number_format($valor_consulta, 2, '.', '')), 10, "0", STR_PAD_LEFT), 0, 10) .
            str_pad(" ", 10, " ") . substr(str_pad(str_replace(".", "", number_format($valor_exames, 2, '.', '')), 10, "0", STR_PAD_LEFT), 0, 10) .
            str_pad(" ", 10, " ") . substr(str_pad(str_replace(".", "", number_format($valor_internacao, 2, '.', '')), 10, "0", STR_PAD_LEFT), 0, 10) .
            str_pad(" ", 10, " ") . substr(str_pad(str_replace(".", "", number_format($valor_outros, 2, '.', '')), 10, "0", STR_PAD_LEFT), 0, 10) .
            str_pad(" ", 10, " ") . substr(str_pad(str_replace(".", "", $valor_odonto), 10, "0", STR_PAD_LEFT), 0, 10) .
            str_pad(" ", 10, " ") . PHP_EOL;
    fwrite($arquivo, sanitizeString($beneficiario));

    $query_prestadores = 'select UNI_PRESTADORES.CODPRESTADOR,
	IF(SUBSTRING(UNI_PRESTADORES.CODPRESTADOR,1,1) = "M" OR FAT_PRODUCAO_SERVICOS_AUX.INF_TIPO_PRESTADOR = 231,"01",
		IF(SUBSTRING(UNI_PRESTADORES.CODPRESTADOR,1,1) = "H" OR FAT_PRODUCAO_SERVICOS_AUX.INF_TIPO_PRESTADOR = 232,"02",
			IF(SUBSTRING(UNI_PRESTADORES.CODPRESTADOR,1,1) = "L" OR FAT_PRODUCAO_SERVICOS_AUX.INF_TIPO_PRESTADOR = 233,"03",
				IF(SUBSTRING(UNI_PRESTADORES.CODPRESTADOR,1,1) = "C" OR FAT_PRODUCAO_SERVICOS_AUX.INF_TIPO_PRESTADOR = 234,"04","05"
	)))) TIPO_PRESTADOR, 
	IF(UNI_EMPRESAS.CNPJ <> "", UNI_EMPRESAS.CNPJ, 
		IF (UNI_PESSOAS.CPF <> "", UNI_PESSOAS.CPF,
			IF(FAT_PRODUCAO_SERVICOS_AUX.INF_CPF_CNPJ_PRESTADOR <> "",FAT_PRODUCAO_SERVICOS_AUX.INF_CPF_CNPJ_PRESTADOR, 
				IF(EMP.CNPJ <> "", EMP.CNPJ, PES.CPF)))) CNPJ_CPF,
	IF(UNI_PRESTADORES.DESCRICAO <> "", UNI_PRESTADORES.DESCRICAO, FAT_PRODUCAO_SERVICOS_AUX.INF_NOME_PRESTADOR) DESCRICAO,
	IF(UNI_EMPRESAS.NOMEFANTASIA IS NOT NULL,UNI_EMPRESAS.NOMEFANTASIA, IF(UNI_PRESTADORES.DESCRICAO IS NOT NULL, UNI_PRESTADORES.DESCRICAO, FAT_PRODUCAO_SERVICOS_AUX.INF_NOME_PRESTADOR)) NOMEFANTASIA,
	IF(UNI_EMPRESAS.RAZAOSOCIAL IS NOT NULL,UNI_EMPRESAS.RAZAOSOCIAL, IF(UNI_PRESTADORES.DESCRICAO IS NOT NULL, UNI_PRESTADORES.DESCRICAO, FAT_PRODUCAO_SERVICOS_AUX.INF_NOME_PRESTADOR)) RAZAOSOCIAL,
	CAST(IF(UNI_PRESTADORES.NUMEROCONSELHO <> "", UNI_PRESTADORES.NUMEROCONSELHO,FAT_PRODUCAO_SERVICOS_AUX.NUMERO_CONS_PROFISSIONAL_EXECUTANTE)AS UNSIGNED) NUMEROCONSELHO, UNI_CONSELHOS.SIGLA, 
	IF(UNI_PRESTADORES.UFCONSELHO <> "", UNI_PRESTADORES.UFCONSELHO, FAT_PRODUCAO_SERVICOS_AUX.UF_CONSELHO_PROFISSIONAL_EXECUTANTE) UFCONSELHO,
	IF(UNI_PRESTADORES.CBO <> "", UNI_PRESTADORES.CBO, 
		IF(FAT_PRODUCAO_SERVICOS_AUX.CBO_PROFISSIONAL_EXECUTANTE <> "", FAT_PRODUCAO_SERVICOS_AUX.CBO_PROFISSIONAL_EXECUTANTE, PREST.CBO)) CBO,
	IF(UNI_CIDADES.COD_IBGE <> "", UNI_CIDADES.COD_IBGE, 
		IF(FAT_PRODUCAO_SERVICOS_AUX.INF_CODIGO_MUNICIPIO <> "", FAT_PRODUCAO_SERVICOS_AUX.INF_CODIGO_MUNICIPIO, CID.COD_IBGE)) COD_IBGE , 
	COALESCE(UNI_PRESTADORES.IDPRESTADOR, FAT_PRODUCAO_SERVICOS.ID_PRESTADOR) ID_PRESTADOR
    from FAT_PRODUCAO
	left join FAT_LOTES_PRESTADORES on FAT_PRODUCAO.ID_LOTE_PRESTADOR = FAT_LOTES_PRESTADORES.ID_LOTE_PRESTADOR
	left join FAT_LOTES on FAT_LOTES_PRESTADORES.ID_LOTE = FAT_LOTES.ID_LOTE
	left join UNI_BENEFICIARIOS on FAT_PRODUCAO.ID_BENEFICIARIO = UNI_BENEFICIARIOS.IDBENEFICIARIO
        left join FAT_PRODUCAO_SERVICOS on FAT_PRODUCAO.ID = FAT_PRODUCAO_SERVICOS.ID_PRODUCAO
        left join FAT_PRODUCAO_SERVICOS_AUX on FAT_PRODUCAO_SERVICOS.ID = FAT_PRODUCAO_SERVICOS_AUX.ID_PRODUCAO_SERVICO
	left join UNI_PRESTADORES on FAT_PRODUCAO_SERVICOS.ID_PRESTADOR = UNI_PRESTADORES.IDPRESTADOR
	left join UNI_EMPRESAS on UNI_PRESTADORES.IDEMPRESA = UNI_EMPRESAS.IDEMPRESA
	left join UNI_PESSOAS on UNI_PRESTADORES.IDPESSOA = UNI_PESSOAS.IDPESSOA
	left join UNI_PRESTADORES PREST on FAT_PRODUCAO_SERVICOS_AUX.NUMERO_CONS_PROFISSIONAL_EXECUTANTE = REPLACE(CAST(PREST.NUMEROCONSELHO AS UNSIGNED),".","")
	left join UNI_EMPRESAS EMP on PREST.IDEMPRESA = EMP.IDEMPRESA
	left join UNI_PESSOAS PES on PREST.IDPESSOA = PES.IDPESSOA
	left join UNI_CONSELHOS on COALESCE(UNI_PRESTADORES.IDCONSELHO, FAT_PRODUCAO_SERVICOS_AUX.ID_CONSELHO_PROFISSIONAL_EXECUTANTE) = UNI_CONSELHOS.IDCONSELHO
	left join UNI_ENDERECOS on UNI_PRESTADORES.ID_ENDERECO_ATEND_PRINC = UNI_ENDERECOS.IDENDERECO
	left join UNI_CIDADES on UNI_ENDERECOS.IDCIDADE = UNI_CIDADES.ID_CIDADE
	left join UNI_ENDERECOS ENDE on PREST.ID_ENDERECO_ATEND_PRINC = ENDE.IDENDERECO
	left join UNI_CIDADES CID on ENDE.IDCIDADE = CID.ID_CIDADE
    where FAT_LOTES.COMPETENCIA = "' . $competencia . '"
    and UNI_BENEFICIARIOS.CODUSUARIO = "0028' . $row_pessoas[5] . '"
    and FAT_PRODUCAO.PEA <> "S"
    GROUP BY 1';

    $resultset_prestadores = mysql_query($query_prestadores);
    while ($row_prestadores = mysql_fetch_row($resultset_prestadores)) {
        $sequencia++;
        $quant_prestador++;

        if (empty($row_prestadores[9])) {
            $cbo = '999999';
        } else {
            if ($row_prestadores[9] == '225205') {
                $cbo = '225151';
            } else {
                $cbo = $row_prestadores[9];
            }
        }

        $prestador = substr(str_pad($sequencia, 8, "0", STR_PAD_LEFT), 0, 8) . '104' . substr(str_pad($row_prestadores[0], 20, " ", STR_PAD_LEFT), 0, 20) . substr(str_pad($row_prestadores[1], 2, " "), 0, 2) . substr(str_pad($row_prestadores[2], 15, " ", STR_PAD_LEFT), 0, 15) .
                substr(str_pad($row_prestadores[3], 70, " "), 0, 70) . substr(str_pad($row_prestadores[4], 40, " "), 0, 40) . substr(str_pad($row_prestadores[5], 40, " "), 0, 40) . substr(str_pad($row_prestadores[6], 8, " ", STR_PAD_LEFT), 0, 8) .
                substr(str_pad($row_prestadores[7], 12, " ", STR_PAD_LEFT), 0, 12) . substr(str_pad($row_prestadores[8], 2, " ", STR_PAD_LEFT), 0, 2) . substr(str_pad($cbo, 6, " ", STR_PAD_LEFT), 0, 6) . substr(str_pad($row_prestadores[10], 7, " ", STR_PAD_LEFT), 0, 7) . PHP_EOL;
        fwrite($arquivo, sanitizeString($prestador));

        $query_procedimentos = 'select FAT_SERVICOS.CODIGO, FAT_PRODUCAO_SERVICOS.QUANTIDADE,
            FAT_PRODUCAO_SERVICOS_VALORES.CO_TAXA + FAT_PRODUCAO_SERVICOS_VALORES.CO_VALOR + FAT_PRODUCAO_SERVICOS_VALORES.FILME_TAXA +
            FAT_PRODUCAO_SERVICOS_VALORES.FILME_VALOR + FAT_PRODUCAO_SERVICOS_VALORES.HM_TAXA + FAT_PRODUCAO_SERVICOS_VALORES.HM_VALOR TOTAL,
            FAT_PRODUCAO_AUX.VALOR_COPARTICIPACAO,
            IF(FAT_PRODUCAO_AUX.DATA_BAIXA is not null, date_format(FAT_PRODUCAO_AUX.DATA_BAIXA,"%Y%m%d"), date_format(FAT_PRODUCAO_SERVICOS.DATA,"%Y%m%d")) DATA_ATEND,
            IF(FAT_PRODUCAO_AUX.DATA_ALTA is not null, date_format(FAT_PRODUCAO_AUX.DATA_ALTA,"%Y%m%d"), date_format(FAT_PRODUCAO_SERVICOS.DATA,"%Y%m%d")) DATA_CONC,
            IF(FAT_PRODUCAO.TIPO_GUIA = 129,"01",
                    IF(FAT_PRODUCAO.TIPO_GUIA = 130,"02",
                            IF(FAT_PRODUCAO.TIPO_GUIA = 131,"03","04"
            ))) CATEGORIA,
            UNI_DOMINIOS_TERMOS_CODIGO.CODIGO,
            FAT_SERVICOS.DESCRICAO
        from FAT_PRODUCAO
            left join FAT_LOTES_PRESTADORES on FAT_PRODUCAO.ID_LOTE_PRESTADOR = FAT_LOTES_PRESTADORES.ID_LOTE_PRESTADOR
            left join FAT_LOTES on FAT_LOTES_PRESTADORES.ID_LOTE = FAT_LOTES.ID_LOTE
            left join UNI_BENEFICIARIOS on FAT_PRODUCAO.ID_BENEFICIARIO = UNI_BENEFICIARIOS.IDBENEFICIARIO
            left join FAT_PRODUCAO_SERVICOS on FAT_PRODUCAO.ID = FAT_PRODUCAO_SERVICOS.ID_PRODUCAO
            left join FAT_PRODUCAO_SERVICOS_VALORES on FAT_PRODUCAO_SERVICOS.ID = FAT_PRODUCAO_SERVICOS_VALORES.ID_PRODUCAO_SERVICO
            left join UNI_PRESTADORES on FAT_PRODUCAO_SERVICOS.ID_PRESTADOR = UNI_PRESTADORES.IDPRESTADOR
            left join FAT_SERVICOS on FAT_PRODUCAO_SERVICOS.ID_SERVICO = FAT_SERVICOS.ID
            left join FAT_PRODUCAO_AUX on FAT_PRODUCAO.ID = FAT_PRODUCAO_AUX.ID_PRODUCAO
            left join UNI_DOMINIOS_TERMOS on FAT_PRODUCAO_AUX.MOTIVO_ENCERRAMENTO = UNI_DOMINIOS_TERMOS.ID_TERMO
            left join UNI_DOMINIOS_TERMOS_CODIGO on UNI_DOMINIOS_TERMOS.ID_TERMO = UNI_DOMINIOS_TERMOS_CODIGO.ID_DOMINIO_TERMO AND UNI_DOMINIOS_TERMOS_CODIGO.PARAMETRO = "TISS3"
        where FAT_LOTES.COMPETENCIA = "' . $competencia . '"
        and UNI_BENEFICIARIOS.CODUSUARIO = "0028' . $row_pessoas[5] . '"
        and FAT_PRODUCAO_SERVICOS.ID_PRESTADOR = ' . $row_prestadores[11] . '
        group by FAT_PRODUCAO_SERVICOS.ID';
        $resultset_procedimentos = mysql_query($query_procedimentos);
        while ($row_procedimentos = mysql_fetch_row($resultset_procedimentos)) {
            $sequencia++;
            $quant_procedimentos++;

            $procedimentos = str_pad(substr(str_pad($sequencia, 8, "0", STR_PAD_LEFT), 0, 8) . '105' .
                            substr(str_pad($row_procedimentos[0], 10, " "), 0, 10) . substr(str_pad(str_replace(".", "", $row_procedimentos[1]), 8, "0", STR_PAD_LEFT), 0, 8) . substr(str_pad(str_replace(".", "", $row_procedimentos[2]), 10, "0", STR_PAD_LEFT), 0, 10) .
                            substr(str_pad(str_replace(".", "", $row_procedimentos[3]), 10, "0", STR_PAD_LEFT), 0, 10) . substr(str_pad($row_procedimentos[4], 8, " "), 0, 8) . substr(str_pad($row_procedimentos[5], 8, " "), 0, 8) . substr(str_pad($row_procedimentos[6], 2, " "), 0, 2) . substr(str_pad($row_procedimentos[7], 2, "0"), 0, 2) .
                            substr(str_pad(sanitizeString($row_procedimentos[8]), 500, " "), 0, 500), 569, " ") . PHP_EOL;
            fwrite($arquivo, $procedimentos);
        }
    }
}
$sequencia++;
$trailer = str_pad($sequencia, 8, "0", STR_PAD_LEFT) . '109' .
        str_pad("0", 7, "0", STR_PAD_LEFT) . str_pad($quant_beneficiario, 7, "0", STR_PAD_LEFT) .
        str_pad($quant_prestador, 7, "0", STR_PAD_LEFT) . str_pad($quant_procedimentos, 7, "0", STR_PAD_LEFT);
fwrite($arquivo, sanitizeString($trailer));

/* a função fwrite escreve o valor da variável $texto no arquivo.txt se o arquivo não existe o php cria o arquivo */

if (isset($nome_arquivo) && file_exists($nome_arquivo)) {
    switch (strtolower(substr(strrchr(basename($nome_arquivo), "."), 1))) {
        case "pdf": $tipo = "application/pdf";
            break;
        case "exe": $tipo = "application/octet-stream";
            break;
        case "zip": $tipo = "application/zip";
            break;
        case "doc": $tipo = "application/msword";
            break;
        case "xls": $tipo = "application/vnd.ms-excel";
            break;
        case "ppt": $tipo = "application/vnd.ms-powerpoint";
            break;
        case "gif": $tipo = "image/gif";
            break;
        case "png": $tipo = "image/png";
            break;
        case "jpg": $tipo = "image/jpg";
            break;
        case "mp3": $tipo = "audio/mpeg";
            break;
        case "php": // deixar vazio por seurança
        case "htm": // deixar vazio por seurança
        case "html": // deixar vazio por seurança
    }
    header("Content-Type: " . $tipo); // informa o tipo do arquivo ao navegador
    header("Content-Length: " . filesize($nome_arquivo)); // informa o tamanho do arquivo ao navegador
    header("Content-Disposition: attachment; filename=" . basename($nome_arquivo)); // informa ao navegador que é tipo anexo e faz abrir a janela de download, tambem informa o nome do arquivo
    readfile($nome_arquivo); // lê o arquivo
    exit; // aborta pós-ações   
}

/* a função fclose retira o arquivo.txt da memória o servidor */
fclose($arquivo);

unlink($nome_arquivo);

echo '<script>alert("Arquivo gerado com Sucesso!")</script>';
echo '<script type="text/javascript">location.href = "extrato_beneficiario.php";</script>';
