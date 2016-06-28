<?php

set_time_limit(600);

error_reporting(0);

include './conectorBD.php';

$conexao = new conexao();
$conexao->conecta();

/* Função fopen usada para abrir arquivo, ou seja, joga-lo na memória do servidor, neste caso o arquivo ainda não existe.
  o “w” quer dizer write, que o arquivo pode ser escrito */

$nome_arquivo = 'EB' . date('Y') . date('m') . '.028';

$arquivo = fopen('F://TEMP/' . $nome_arquivo, 'w');

$cod_unimed = '0028';
$competencia = $_POST['competencia'];

$header = '00000001101' . $cod_unimed . date('Y') . date('m') . date('d') . $competencia . '010201' . PHP_EOL;

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
GROUP BY 1';
$resultset_pessoas = mysql_query($query_pessoas);
while ($row_pessoas = mysql_fetch_row($resultset_pessoas)) {
    $sequencia++;
    $quant_beneficiario++;
    
    $query_coparticipacao = 'select SUM(FAT_PRODUCAO_AUX.VALOR_COPARTICIPACAO) VALOR_COPARTICIPACAO from FAT_PRODUCAO
	left join FAT_LOTES_PRESTADORES on FAT_PRODUCAO.ID_LOTE_PRESTADOR = FAT_LOTES_PRESTADORES.ID_LOTE_PRESTADOR
	left join FAT_LOTES on FAT_LOTES_PRESTADORES.ID_LOTE = FAT_LOTES.ID_LOTE
	left join UNI_BENEFICIARIOS on FAT_PRODUCAO.ID_BENEFICIARIO = UNI_BENEFICIARIOS.IDBENEFICIARIO
	left join FAT_PRODUCAO_AUX on FAT_PRODUCAO.ID = FAT_PRODUCAO_AUX.ID_PRODUCAO
    where FAT_LOTES.COMPETENCIA = "' . $competencia . '"
    and UNI_BENEFICIARIOS.CODUSUARIO = "0028' . $row_pessoas[5] . '"';
    $resultset_coparticipacao = mysql_query($query_coparticipacao);
    $row_coparticipacao = mysql_fetch_row($resultset_coparticipacao);

    $query_extrato = 'select SUM(FAT_PRODUCAO_SERVICOS_VALORES.CO_TAXA) + SUM(FAT_PRODUCAO_SERVICOS_VALORES.CO_VALOR) + SUM(FAT_PRODUCAO_SERVICOS_VALORES.FILME_TAXA) +
    SUM(FAT_PRODUCAO_SERVICOS_VALORES.FILME_VALOR) + SUM(FAT_PRODUCAO_SERVICOS_VALORES.HM_TAXA) + SUM(FAT_PRODUCAO_SERVICOS_VALORES.HM_VALOR) EXTRATO
    from FAT_PRODUCAO
	left join FAT_LOTES_PRESTADORES on FAT_PRODUCAO.ID_LOTE_PRESTADOR = FAT_LOTES_PRESTADORES.ID_LOTE_PRESTADOR
	left join FAT_LOTES on FAT_LOTES_PRESTADORES.ID_LOTE = FAT_LOTES.ID_LOTE
	left join UNI_BENEFICIARIOS on FAT_PRODUCAO.ID_BENEFICIARIO = UNI_BENEFICIARIOS.IDBENEFICIARIO
	left join FAT_PRODUCAO_AUX on FAT_PRODUCAO.ID = FAT_PRODUCAO_AUX.ID_PRODUCAO
	left join FAT_PRODUCAO_SERVICOS on FAT_PRODUCAO.ID = FAT_PRODUCAO_SERVICOS.ID_PRODUCAO
	left join FAT_PRODUCAO_SERVICOS_VALORES on FAT_PRODUCAO_SERVICOS.ID = FAT_PRODUCAO_SERVICOS_VALORES.ID_PRODUCAO_SERVICO
    where FAT_LOTES.COMPETENCIA = "' . $competencia . '"
    and UNI_BENEFICIARIOS.CODUSUARIO = "0028' . $row_pessoas[5] . '"';
    $resultset_extrato = mysql_query($query_extrato);
    $row_extrato = mysql_fetch_row($resultset_extrato);

    $query_coparticipacao_titular = 'select SUM(FAT_PRODUCAO_AUX.VALOR_COPARTICIPACAO) VALOR_COPARTICIPACAO from FAT_PRODUCAO
	left join FAT_LOTES_PRESTADORES on FAT_PRODUCAO.ID_LOTE_PRESTADOR = FAT_LOTES_PRESTADORES.ID_LOTE_PRESTADOR
	left join FAT_LOTES on FAT_LOTES_PRESTADORES.ID_LOTE = FAT_LOTES.ID_LOTE
	left join UNI_BENEFICIARIOS on FAT_PRODUCAO.ID_BENEFICIARIO = UNI_BENEFICIARIOS.IDBENEFICIARIO
        left join UNI_BENEFICIARIOS TIT on UNI_BENEFICIARIOS.IDTITULAR = TIT.IDBENEFICIARIO
	left join FAT_PRODUCAO_AUX on FAT_PRODUCAO.ID = FAT_PRODUCAO_AUX.ID_PRODUCAO
    where FAT_LOTES.COMPETENCIA = "' . $competencia . '"
    and TIT.CODUSUARIO = "0028' . $row_pessoas[7] . '"';
    $resultset_coparticipacao_titular = mysql_query($query_coparticipacao_titular);
    $row_coparticipacao_titular = mysql_fetch_row($resultset_coparticipacao_titular);

    $query_extrato_titular = 'select SUM(FAT_PRODUCAO_SERVICOS_VALORES.CO_TAXA) + SUM(FAT_PRODUCAO_SERVICOS_VALORES.CO_VALOR) + SUM(FAT_PRODUCAO_SERVICOS_VALORES.FILME_TAXA) +
    SUM(FAT_PRODUCAO_SERVICOS_VALORES.FILME_VALOR) + SUM(FAT_PRODUCAO_SERVICOS_VALORES.HM_TAXA) + SUM(FAT_PRODUCAO_SERVICOS_VALORES.HM_VALOR) EXTRATO
    from FAT_PRODUCAO
	left join FAT_LOTES_PRESTADORES on FAT_PRODUCAO.ID_LOTE_PRESTADOR = FAT_LOTES_PRESTADORES.ID_LOTE_PRESTADOR
	left join FAT_LOTES on FAT_LOTES_PRESTADORES.ID_LOTE = FAT_LOTES.ID_LOTE
	left join UNI_BENEFICIARIOS on FAT_PRODUCAO.ID_BENEFICIARIO = UNI_BENEFICIARIOS.IDBENEFICIARIO
        left join UNI_BENEFICIARIOS TIT on UNI_BENEFICIARIOS.IDTITULAR = TIT.IDBENEFICIARIO
	left join FAT_PRODUCAO_AUX on FAT_PRODUCAO.ID = FAT_PRODUCAO_AUX.ID_PRODUCAO
	left join FAT_PRODUCAO_SERVICOS on FAT_PRODUCAO.ID = FAT_PRODUCAO_SERVICOS.ID_PRODUCAO
	left join FAT_PRODUCAO_SERVICOS_VALORES on FAT_PRODUCAO_SERVICOS.ID = FAT_PRODUCAO_SERVICOS_VALORES.ID_PRODUCAO_SERVICO
    where FAT_LOTES.COMPETENCIA = "' . $competencia . '"
    and TIT.CODUSUARIO = "0028' . $row_pessoas[7] . '"';
    $resultset_extrato_titular = mysql_query($query_extrato_titular);
    $row_extrato_titular = mysql_fetch_row($resultset_extrato_titular);

    $beneficiario = str_pad($sequencia, 8, "0", STR_PAD_LEFT) . '103' . str_pad($row_pessoas[0], 70, " ") . str_pad($row_pessoas[1], 4, "0", STR_PAD_LEFT) .
            str_pad($row_pessoas[2], 2, "0", STR_PAD_LEFT) . str_pad($row_pessoas[3], 2, "0", STR_PAD_LEFT) . '0028' . str_pad($row_pessoas[4], 15, " ") .
            $row_pessoas[5] . $row_pessoas[6] . $row_pessoas[7] . str_pad($row_pessoas[8], 70, " ") . str_pad(str_replace(".", ",", $row_extrato[0]), 10, "0", STR_PAD_LEFT) .
            str_pad(str_replace(".", ",", $row_coparticipacao[0]), 10, "0", STR_PAD_LEFT) . str_pad(str_replace(".", ",", $row_extrato_titular[0]), 10, "0", STR_PAD_LEFT) .
            str_pad(str_replace(".", ",", $row_coparticipacao_titular[0]), 10, "0", STR_PAD_LEFT) . str_pad($row_pessoas[9], 15, " ", STR_PAD_LEFT) .
            str_pad($row_pessoas[10], 20, " ", STR_PAD_LEFT) . str_pad($row_pessoas[11], 60, " ") . PHP_EOL;
    fwrite($arquivo, $beneficiario);

    $query_prestadores = 'select UNI_PRESTADORES.CODPRESTADOR,
	IF(SUBSTRING(UNI_PRESTADORES.CODPRESTADOR,1,1) = "M","01",
		IF(SUBSTRING(UNI_PRESTADORES.CODPRESTADOR,1,1) = "H","02",
			IF(SUBSTRING(UNI_PRESTADORES.CODPRESTADOR,1,1) = "L","03",
				IF(SUBSTRING(UNI_PRESTADORES.CODPRESTADOR,1,1) = "C","04","01"
	)))) TIPO_PRESTADOR,
	IF(UNI_EMPRESAS.CNPJ IS NOT NULL, UNI_EMPRESAS.CNPJ, UNI_PESSOAS.CPF) CNPJ_CPF,
	UNI_PRESTADORES.DESCRICAO,
	IF(UNI_EMPRESAS.NOMEFANTASIA IS NOT NULL,UNI_EMPRESAS.NOMEFANTASIA, UNI_PRESTADORES.DESCRICAO) NOMEFANTASIA,
	IF(UNI_EMPRESAS.RAZAOSOCIAL IS NOT NULL,UNI_EMPRESAS.RAZAOSOCIAL, UNI_PRESTADORES.DESCRICAO) RAZAOSOCIAL,
	UNI_PRESTADORES.NUMEROCONSELHO, UNI_CONSELHOS.SIGLA, UNI_PRESTADORES.UFCONSELHO, UNI_PRESTADORES.CBO,
	UNI_CIDADES.COD_IBGE, UNI_PRESTADORES.IDPRESTADOR
    from FAT_PRODUCAO
	left join FAT_LOTES_PRESTADORES on FAT_PRODUCAO.ID_LOTE_PRESTADOR = FAT_LOTES_PRESTADORES.ID_LOTE_PRESTADOR
	left join FAT_LOTES on FAT_LOTES_PRESTADORES.ID_LOTE = FAT_LOTES.ID_LOTE
	left join UNI_BENEFICIARIOS on FAT_PRODUCAO.ID_BENEFICIARIO = UNI_BENEFICIARIOS.IDBENEFICIARIO
	left join UNI_PRESTADORES on FAT_LOTES_PRESTADORES.ID_PRESTADOR = UNI_PRESTADORES.IDPRESTADOR
	left join UNI_EMPRESAS on UNI_PRESTADORES.IDEMPRESA = UNI_EMPRESAS.IDEMPRESA
	left join UNI_PESSOAS on UNI_PRESTADORES.IDPESSOA = UNI_PESSOAS.IDPESSOA
	left join UNI_CONSELHOS on UNI_PRESTADORES.IDCONSELHO = UNI_CONSELHOS.IDCONSELHO
	left join UNI_ENDERECOS on UNI_PRESTADORES.ID_ENDERECO_ATEND_PRINC = UNI_ENDERECOS.IDENDERECO
	left join UNI_CIDADES on UNI_ENDERECOS.IDCIDADE = UNI_CIDADES.ID_CIDADE
    where FAT_LOTES.COMPETENCIA = "' . $competencia . '"
    and UNI_BENEFICIARIOS.CODUSUARIO = "0028' . $row_pessoas[5] . '"
    GROUP BY 1';
    $resultset_prestadores = mysql_query($query_prestadores);
    while ($row_prestadores = mysql_fetch_row($resultset_prestadores)) {
        $sequencia++;
        $quant_prestador++;
                
        $prestador = str_pad($sequencia, 8, "0", STR_PAD_LEFT) . '104' . str_pad($row_prestadores[0], 20, " ", STR_PAD_LEFT) . $row_prestadores[1] . str_pad($row_prestadores[2], 15, " ", STR_PAD_LEFT) .
                str_pad($row_prestadores[3], 70, " ") . str_pad($row_prestadores[4], 40, " ") . str_pad($row_prestadores[5], 40, " ") . str_pad($row_prestadores[6], 8, " ", STR_PAD_LEFT) .
                str_pad($row_prestadores[7], 12, " ", STR_PAD_LEFT) . str_pad($row_prestadores[8], 2, " ", STR_PAD_LEFT) . str_pad($row_prestadores[9], 6, " ", STR_PAD_LEFT) . str_pad($row_prestadores[10], 7, " ", STR_PAD_LEFT) . PHP_EOL;
        fwrite($arquivo, $prestador);

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
            left join UNI_PRESTADORES on FAT_LOTES_PRESTADORES.ID_PRESTADOR = UNI_PRESTADORES.IDPRESTADOR
            left join FAT_PRODUCAO_SERVICOS on FAT_PRODUCAO.ID = FAT_PRODUCAO_SERVICOS.ID_PRODUCAO
            left join FAT_PRODUCAO_SERVICOS_VALORES on FAT_PRODUCAO_SERVICOS.ID = FAT_PRODUCAO_SERVICOS_VALORES.ID_PRODUCAO_SERVICO
            left join FAT_SERVICOS on FAT_PRODUCAO_SERVICOS.ID_SERVICO = FAT_SERVICOS.ID
            left join FAT_PRODUCAO_AUX on FAT_PRODUCAO.ID = FAT_PRODUCAO_AUX.ID_PRODUCAO
            left join UNI_DOMINIOS_TERMOS on FAT_PRODUCAO_AUX.MOTIVO_ENCERRAMENTO = UNI_DOMINIOS_TERMOS.ID_TERMO
            left join UNI_DOMINIOS_TERMOS_CODIGO on UNI_DOMINIOS_TERMOS.ID_TERMO = UNI_DOMINIOS_TERMOS_CODIGO.ID_DOMINIO_TERMO AND UNI_DOMINIOS_TERMOS_CODIGO.PARAMETRO = "TISS3"
        where FAT_LOTES.COMPETENCIA = "' . $competencia . '"
        and UNI_BENEFICIARIOS.CODUSUARIO = "0028' . $row_pessoas[5] . '"
        and FAT_LOTES_PRESTADORES.ID_PRESTADOR = ' . $row_prestadores[11] . '
        group by FAT_PRODUCAO_SERVICOS.ID';
        $resultset_procedimentos = mysql_query($query_procedimentos);
        while ($row_procedimentos = mysql_fetch_row($resultset_procedimentos)) {
            $sequencia++;
            $quant_procedimentos++;
            
            $procedimentos = str_pad($sequencia, 8, "0", STR_PAD_LEFT) . '105' .
                    str_pad($row_procedimentos[0], 10, " ") . str_pad($row_procedimentos[1], 8, "0", STR_PAD_LEFT) . str_pad($row_procedimentos[2], 10, "0", STR_PAD_LEFT) .
                    str_pad($row_procedimentos[3], 8, "0", STR_PAD_LEFT) . $row_procedimentos[4] . $row_procedimentos[5] . $row_procedimentos[6] . str_pad($row_procedimentos[7], 2, "0") .
                    str_pad($row_procedimentos[8], 500, " ") . PHP_EOL;
            fwrite($arquivo, $procedimentos);
        }
    }
}
$sequencia++;
$trailer = str_pad($sequencia, 8, "0", STR_PAD_LEFT) . '109' .
        str_pad("0", 7, "0", STR_PAD_LEFT) . str_pad($quant_beneficiario, 7, "0", STR_PAD_LEFT) .
        str_pad($quant_prestador, 7, "0", STR_PAD_LEFT) . str_pad($quant_procedimentos, 7, "0", STR_PAD_LEFT);
fwrite($arquivo, $trailer);

/* a função fwrite escreve o valor da variável $texto no arquivo.txt se o arquivo não existe o php cria o arquivo */


/* a função fclose retira o arquivo.txt da memória o servidor */
fclose($arquivo);

echo '<script>alert("Arquivo gerado com Sucesso!")</script>';
echo '<script type="text/javascript">location.href = "extrato_beneficiario.php";</script>';
