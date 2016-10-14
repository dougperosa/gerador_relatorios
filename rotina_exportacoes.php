<?php

set_time_limit(600);

error_reporting(0);

include './conectorBD.php';

$conexao = new conexao();
$conexao->conecta();

$abertos = $_POST['abertos'];
$filial = $_POST['filiais'];
$operacao = $_POST['operacao'];
$cnpjcpf = $_POST['cnpjcpf'];
$emissaoinicio = $_POST['dataemissaoinicio'];
$emissaofinal = $_POST['dataemissaofinal'];
$vencinicio = $_POST['datavencinicio'];
$vencfinal = $_POST['datavencfinal'];

$where = null;
$union = null;

if (isset($_POST['abertos'])) {
    $where = ' AND SITUACAO = "A"';
}

if (!empty($filial)) {
    $where .= ' AND FIN_FINANCEIRO.ID_FILIAL_FINANCEIRO = ' . $filial;
}

if (!empty($operacao)) {
    $where .= ' AND FIN_FINANCEIRO.ID_OPERACAO = ' . $operacao;
}

if (!empty($cnpjcpf)) {
    $where .= ' AND (UNI_EMPRESAS.CNPJ = "' . $cnpjcpf . '" OR UNI_PESSOAS.CPF = "' . $cnpjcpf . '")';
}

if (!empty($emissaoinicio)) {
    $where .= ' AND FIN_FINANCEIRO.DATA_EMISSAO >= "' . $emissaoinicio . '"';
}

if (!empty($emissaofinal)) {
    $where .= ' AND FIN_FINANCEIRO.DATA_EMISSAO <= "' . $emissaofinal . '"';
}

if (!empty($vencinicio)) {
    $where .= ' AND FIN_FINANCEIRO.DATA_VENCIMENTO >= "' . $vencinicio . '"';
}

if (!empty($vencfinal)) {
    $where .= ' AND FIN_FINANCEIRO.DATA_VENCIMENTO <= "' . $vencfinal . '"';
}

if (isset($_POST['mensalidades'])) {
    $union = ' union all
select UNI_PESSOAS.CPF, NU_PARC, date_format(DT_EMSS,"%d%m%Y") DT_EMSS, date_format(DT_VCTO,"%d%m%Y") DT_VCTO, BT008.VL_MENS, "PF",
	if(UNI_PLANOS.ID_MODELO_CONTRATO = 4 OR UNI_PLANOS.ID_MODELO_CONTRATO = 7, "PFA","PFD") ESPECIE
	from bitmed.BT008 
left join unimed.UNI_BENEFICIARIOS on concat(bitmed.BT008.CD_EMPR,bitmed.BT008.CD_USUA,"00",FN_CALC_DIGITO_UNIMED(concat(bitmed.BT008.CD_EMPR,bitmed.BT008.CD_USUA,"00"))) = unimed.UNI_BENEFICIARIOS.CODUSUARIO	
left join unimed.UNI_PESSOAS on UNI_BENEFICIARIOS.IDPESSOA = UNI_PESSOAS.IDPESSOA
left join unimed.UNI_PLANOS on bitmed.BT008.CD_EMPR = UNI_PLANOS.CODIGOPLANO
where bitmed.BT008.ST_PAGT = "" and NU_PARC <> ""';
}

if (isset($_POST['pendencias'])) {
    $union = $union . ' union all
select UNI_PESSOAS.CPF, CD_PEND, date_format(DT_EMSS,"%d%m%Y") DT_EMSS, date_format(DT_VCTO,"%d%m%Y") DT_VCTO, BT088.VL_PEND, "PF", "PCF" ESPECIE
	from bitmed.BT088 
left join unimed.UNI_BENEFICIARIOS on concat(CD_TER,unimed.FN_CALC_DIGITO_UNIMED(CD_TER)) = unimed.UNI_BENEFICIARIOS.CODUSUARIO	
left join unimed.UNI_PESSOAS on UNI_BENEFICIARIOS.IDPESSOA = UNI_PESSOAS.IDPESSOA
where BT088.ST_PGTO = ""';
}

/* Função fopen usada para abrir arquivo, ou seja, joga-lo na memória do servidor, neste caso o arquivo ainda não existe.
  o “w” quer dizer write, que o arquivo pode ser escrito */

$layout = $_POST['expotacao'];

if ($layout == 'ctpagar') {

    $nome_arquivo = 'Contas_Pagar.txt';

    $arquivo = fopen($nome_arquivo, 'w');

    $arquivo_erro = fopen('F://TEMP/Exportacao_Erros.txt', 'w');

//100
    $brancos = null;
    $cod_refer = date('dmHis');
    $dat_transacao = '31122016';
    $val_tot_lote_impl = null;
    $cod_estab = null;
    if ($filial == 2) {
        $cod_estab = '102';
    } else if ($filial == 3) {
        $cod_estab = '103';
    } else if ($filial == 4) {
        $cod_estab = '104';
    } else if ($filial == 5) {
        $cod_estab = '105';
    } else if ($filial == 6) {
        $cod_estab = '106';
    } else if ($filial == 7) {
        $cod_estab = '107';
    } else if ($filial == 22) {
        $cod_estab = '108';
    } else if ($filial == 23) {
        $cod_estab = '109';
    } else {
        $cod_estab = '101';
    }

    $cod_estab_ext = null;

    $registro100 = '100' . str_pad($brancos, 3, " ", STR_PAD_LEFT) . $cod_refer . $dat_transacao . str_pad(str_replace(".", "", $val_tot_lote_impl), 14, "0", STR_PAD_LEFT) . $cod_estab . PHP_EOL;

    fwrite($arquivo, $registro100);

//200;
    $cdn_fornecedor = null;
    $cod_espec_docto = null;
    $cod_ser_docto = null;
    $cod_tit_ap = null;
    $cod_parcela = null;
    $dat_emis_docto = null;
    $dat_vencto_tit_ap = null;
    $dat_prev_pagto = null;
    $cod_finalid_econ_ext = null;
    $val_tit_ap = null;
    $dat_desconto = null;
    $val_desconto = null;
    $val_perc_desc = null;
    $num_dias_atraso = null;
    $val_jur_dia_atraso = null;
    $val_perc_jur_dia_atraso = null;
    $val_perc_multa_atraso = null;
    $cod_portad_ext = null;
    $cod_modalid_ext = null;
    $cod_apol_segur = null;
    $cod_seguradora = null;
    $cod_arrendador = null;
    $cod_contrat_leas = null;
    $cod_portador = '41';
    $cod_indic_econ = 'REAL';
    $cod_forma_pagto = null;
    $des_histor_padr = null;
    $val_cotac_indic_econ = null;
    $num_ord_invest = null;

    //400
    $val_aprop_ctbl = null;
    $cod_plano_cta_ctbl = 'ANS2016';
    $cod_cta_ctbl = '91111911111110090';
    $cod_plano_ccusto = '';
    $cod_unid_negoc = null;
    $cod_tip_fluxo_financ = '2.1.10.10.03';

    $query = "select IF(FIN_FINANCEIRO.ID_EMPRESA IS NOT NULL, UNI_EMPRESAS.CNPJ, UNI_PESSOAS.CPF) CNPJ_CPF,
            FIN_FINANCEIRO.NUMERO_DOCUMENTO, date_format(FIN_FINANCEIRO.DATA_EMISSAO,'%d%m%Y') DATA_EMISSAO, 
            date_format(FIN_FINANCEIRO.DATA_VENCIMENTO,'%d%m%Y') DATA_VENC, FIN_FINANCEIRO.VALOR_ORIGINAL, 
            FIN_FINANCEIRO.ID_PESSOA, FIN_FINANCEIRO.ID_EMPRESA, FIN_FINANCEIRO.ID_OPERACAO
    from FIN_FINANCEIRO
    left join UNI_EMPRESAS on FIN_FINANCEIRO.ID_EMPRESA = UNI_EMPRESAS.IDEMPRESA
    left join UNI_PESSOAS on FIN_FINANCEIRO.ID_PESSOA = UNI_PESSOAS.IDPESSOA
    where FIN_FINANCEIRO.TIPO = 'S' " . $where . " ORDER BY FIN_FINANCEIRO.ID DESC";

    $resultset = mysql_query($query);
    while ($row = mysql_fetch_row($resultset)) {

        //VERIFICA ESPECIE E UN. NEGOCIO
        $query_prestadores = 'SELECT CODPRESTADOR FROM UNI_PRESTADORES WHERE IDPESSOA = ' . $row[4];
        $resultset_prestadores = mysql_query($query_prestadores);
        $row_prestadores = mysql_fetch_row($resultset_prestadores);

        $query_plano = 'SELECT CAMARA FROM UNI_UNIMEDS WHERE IDEMPRESA = ' . $row[5];
        $resultset_plano = mysql_query($query_plano);
        $row_plano = mysql_fetch_row($resultset_plano);

        if (!empty($row_prestadores[0])) {
            if (substr($row_prestadores[0], 0, 1) == 'M') {
                $cod_espec_docto = 'COO';
                $cod_unid_negoc = 'COU';
            } else {
                $cod_espec_docto = 'PRE';
                $cod_unid_negoc = 'POU';
            }
        } else if (!empty($row_plano[0])) {
            if ($row_plano[0] == 'C1') {
                $cod_espec_docto = 'INE';
            } else {
                $cod_espec_docto = 'INC';
            }
            $cod_unid_negoc = 'PLS';
        } else if ($filial != 1) {
            if ($filial == 2 OR $filial == 3) {
                $cod_unid_negoc = 'FAR';
                if ($row[6] == '842') {
                    $cod_espec_docto = 'DM';
                } else {
                    $cod_espec_docto = 'DF';
                }
            } else {
                $cod_unid_negoc = 'UNI';
                if ($row[6] == '862') {
                    $cod_espec_docto = 'DN';
                } else {
                    $cod_espec_docto = 'DS';
                }
            }
        } else {
            $cod_espec_docto = 'DP';
            $cod_unid_negoc = 'PLS';
        }
        //FIM VERIFICA ESPECIE E UN. NEGOCIO
        //200
        $cdn_fornecedor = $row[0];
        $cod_ser_docto = '1';
        $cod_tit_ap = $row[1];
        $cod_parcela = '01';
        $dat_emis_docto = $row[2];
        $dat_vencto_tit_ap = $row[3];
        $val_tit_ap = $row[4];
        $val_tot_lote_impl = $val_tot_lote_impl + $row[1];

        if ($cdn_fornecedor == null) {
            fwrite($arquivo_erro, 'NOTA ' . $cod_tit_ap . ' SEM CNPJ/CPF.' . PHP_EOL);
        } else {

            $registro200 = '200' . str_pad($brancos, 9, " ", STR_PAD_LEFT) . str_pad($cod_espec_docto, 3, " ", STR_PAD_LEFT) . str_pad($brancos, 3, " ", STR_PAD_LEFT) .
                    str_pad($brancos, 10, " ", STR_PAD_LEFT) . $cod_parcela . $dat_emis_docto . $dat_vencto_tit_ap .
                    str_pad($brancos, 18, " ", STR_PAD_LEFT) . str_pad(str_replace(".", "", $val_tit_ap), 11, "0", STR_PAD_LEFT) . str_pad($brancos, 98, " ", STR_PAD_LEFT) .
                    str_pad($cod_portador, 5, " ", STR_PAD_LEFT) . str_pad($cod_indic_econ, 8, " ", STR_PAD_LEFT) . str_pad($brancos, 236, " ", STR_PAD_LEFT) .
                    str_pad($cod_ser_docto, 5, " ", STR_PAD_LEFT) . substr(str_pad($cod_tit_ap, 16, " ", STR_PAD_LEFT), 0, 16) . str_pad($cdn_fornecedor, 14, " ") . PHP_EOL;
            fwrite($arquivo, $registro200);

            //300
            $cod_imposto = null;
            $cod_classif_impto = null;
            $val_rendto_tribut = null;
            $val_aliq_impto = null;
            $val_imposto = null;

            $registro300 = '300' . str_pad($brancos, 23, " ", STR_PAD_LEFT) . str_pad($cod_imposto, 5, " ", STR_PAD_LEFT) . str_pad($cod_classif_impto, 5, " ", STR_PAD_LEFT) .
                    str_pad($brancos, 5, " ", STR_PAD_LEFT) . str_pad(str_replace(".", "", $val_rendto_tribut), 11, "0", STR_PAD_LEFT) . str_pad(str_replace(".", "", $val_aliq_impto), 4, "0", STR_PAD_LEFT) .
                    str_pad(str_replace(".", "", $val_imposto), 11, "0", STR_PAD_LEFT) . PHP_EOL;

            //fwrite($arquivo, $registro300);
            //400
            $val_aprop_ctbl = $val_tit_ap;

            $registro400 = '400' . str_pad($brancos, 71, " ", STR_PAD_LEFT) . str_pad(str_replace(".", "", $val_aprop_ctbl), 11, "0", STR_PAD_LEFT) .
                    str_pad($cod_plano_cta_ctbl, 8, " ", STR_PAD_LEFT) . str_pad($cod_cta_ctbl, 20, " ", STR_PAD_LEFT) . str_pad($cod_plano_ccusto, 8, " ", STR_PAD_LEFT) .
                    str_pad($brancos, 11, " ", STR_PAD_LEFT) . str_pad($cod_unid_negoc, 3, " ", STR_PAD_LEFT) . str_pad($cod_tip_fluxo_financ, 12, " ", STR_PAD_LEFT) . PHP_EOL;

            fwrite($arquivo, $registro400);
        }
    }
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
} else if ($layout == 'ctreceber') {
    $nome_arquivo = 'Contas_Receber.txt';

    $arquivo = fopen($nome_arquivo, 'w');

    $arquivo_erro = fopen('F://TEMP/Exportacao_Erros.txt', 'w');

//100
    $brancos = null;
    $cod_refer = date('dmHis');
    $dat_transacao = '31122016';
    $val_tot_lote_impl_tit_acr = null;
    $cod_estab = null;

    if ($filial == 2) {
        $cod_estab = '102';
    } else if ($filial == 3) {
        $cod_estab = '103';
    } else if ($filial == 4) {
        $cod_estab = '104';
    } else if ($filial == 5) {
        $cod_estab = '105';
    } else if ($filial == 6) {
        $cod_estab = '106';
    } else if ($filial == 7) {
        $cod_estab = '107';
    } else if ($filial == 22) {
        $cod_estab = '108';
    } else if ($filial == 23) {
        $cod_estab = '109';
    } else {
        $cod_estab = '101';
    }

    $registro100 = '100' . str_pad($brancos, 3, " ", STR_PAD_LEFT) . $cod_refer . $dat_transacao . ' ' .
            str_pad(str_replace(".", "", $val_tot_lote_impl_tit_acr), 13, "0", STR_PAD_LEFT) .
            str_pad($brancos, 13, " ", STR_PAD_LEFT) . str_pad($cod_estab, 5, " ") . str_pad($cod_estab, 5, " ") . PHP_EOL;

    fwrite($arquivo, $registro100);

//200;
    $cdn_cliente = null;
    $cod_espec_docto = null;
    $cod_ser_docto = null;
    $cod_tit_acr = null;
    $cod_indic_econ = null;
    $cod_parcela = null;
    $dat_emis_docto = null;
    $dat_vencto_tit_acr = null;
    $val_tit_acr = null;
    $ind_ender_cobr = null;
    $ind_sit_bcia_tit_acr = null;
    $ind_sit_tit_acr = null;
    $vl_base_calculo = null;

    $query = "select IF(FIN_FINANCEIRO.ID_EMPRESA IS NOT NULL, UNI_EMPRESAS.CNPJ, UNI_PESSOAS.CPF) CNPJ_CPF, 
            COALESCE(FIN_FINANCEIRO.NUMERO_DOCUMENTO, FIN_FINANCEIRO.ID) NUMERO_DOCUMENTO, date_format(FIN_FINANCEIRO.DATA_EMISSAO,'%d%m%Y') DATA_EMISSAO, 
            date_format(FIN_FINANCEIRO.DATA_VENCIMENTO,'%d%m%Y') DATA_VENC, FIN_FINANCEIRO.VALOR_ORIGINAL,
            IF(FIN_FINANCEIRO.ID_EMPRESA IS NOT NULL, 'PJ', 'PF') TIPO_PESSOA, 'PSR' ESPECIE
    from FIN_FINANCEIRO
    left join UNI_EMPRESAS on FIN_FINANCEIRO.ID_EMPRESA = UNI_EMPRESAS.IDEMPRESA
    left join UNI_PESSOAS on FIN_FINANCEIRO.ID_PESSOA = UNI_PESSOAS.IDPESSOA
    where FIN_FINANCEIRO.TIPO = 'E' " . $where .
            " union all
    select UNI_EMPRESAS.CNPJ, CD_DUPL, date_format(DT_EMSS,'%d%m%Y') DT_EMSS, date_format(DT_VCTO,'%d%m%Y') DT_VCTO, BT013.VL_DUPL, 'PJ', 
            IF(UNI_PLANOS.CODIGOPLANO = '00283183', 'PFU',
                    IF(UNI_PLANOS.CODIGOPLANO = '00286058', 'PME',
                            if(UNI_PLANOS.NATUREZA = 4 AND UNI_PLANOS.ID_MODELO_CONTRATO = 4, 'PCA',
                            if(UNI_PLANOS.NATUREZA = 4 AND UNI_PLANOS.ID_MODELO_CONTRATO <> 4, 'PCD',
                            if(UNI_PLANOS.NATUREZA <> 4 AND UNI_PLANOS.ID_MODELO_CONTRATO = 4, 'PEA',
                            if(UNI_PLANOS.NATUREZA <> 4 AND UNI_PLANOS.ID_MODELO_CONTRATO <> 4, 'PED',''))))
                    )
            )
            from bitmed.BT013 
    left join unimed.UNI_PLANOS on bitmed.BT013.CD_EMPR = UNI_PLANOS.CODIGOPLANO
    left join unimed.UNI_EMPRESAS on UNI_PLANOS.IDEMPRESA = UNI_EMPRESAS.IDEMPRESA
    where BT013.DT_PAGT is null " . $union;

    $resultset = mysql_query($query);
    while ($row = mysql_fetch_row($resultset)) {

        //VERIFICA ESPECIE E UN. NEGOCIO
        $query_plano = 'SELECT CAMARA FROM UNI_UNIMEDS WHERE IDEMPRESA = ' . $row[5];
        $resultset_plano = mysql_query($query_plano);
        $row_plano = mysql_fetch_row($resultset_plano);

        if ($row[5] == 'PJ') {
            if (!empty($row_plano[0])) {
                if ($row_plano[0] == 'C1') {
                    $cod_espec_docto = 'INE';
                } else {
                    $cod_espec_docto = 'INC';
                }
            } else {
                $cod_espec_docto = $row[6];
            }
        } else {
            $cod_espec_docto = $row[6];
        }
        //FIM VERIFICA ESPECIE E UN. NEGOCIO
        //200
        $cdn_cliente = $row[0];
        $cod_espec_docto = $especie;
        $cod_ser_docto = null;
        $cod_tit_acr = $row[1];
        $cod_indic_econ = 'REAL';
        $cod_parcela = '01';
        $dat_emis_docto = $row[2];
        $dat_vencto_tit_acr = $row[3];
        $val_tit_acr = $row[4];

        $ind_ender_cobr = 'Cliente';
        $ind_sit_bcia_tit_acr = 'Liberado';
        $ind_sit_tit_acr = 'Normal';
        $vl_base_calculo = null;

        if ($cdn_cliente == null) {
            fwrite($arquivo_erro, 'NOTA ' . $cod_tit_acr . ' SEM CNPJ/CPF.' . PHP_EOL);
        } else {

            $registro200 = '200' . str_pad($brancos, 10, " ", STR_PAD_LEFT) . str_pad($cod_espec_docto, 3, " ", STR_PAD_LEFT) . str_pad($branco, 3, " ", STR_PAD_LEFT) .
                    str_pad($branco, 10, " ", STR_PAD_LEFT) . str_pad($cod_indic_econ, 8, " ", STR_PAD_LEFT) . str_pad($cod_parcela, 2, " ", STR_PAD_LEFT) . str_pad($dat_emis_docto, 8, " ") . str_pad($dat_vencto_tit_acr, 8, " ") .
                    str_pad($brancos, 19, " ", STR_PAD_LEFT) . str_pad(str_replace(".", "", $val_tit_acr), 11, "0", STR_PAD_LEFT) . str_pad($brancos, 205, " ", STR_PAD_LEFT) .
                    str_pad($ind_ender_cobr, 15, " ", STR_PAD_LEFT) . str_pad($brancos, 21, " ", STR_PAD_LEFT) . str_pad($ind_sit_bcia_tit_acr, 12, " ", STR_PAD_LEFT) .
                    str_pad($ind_sit_tit_acr, 13, " ", STR_PAD_LEFT) . str_pad($brancos, 201, " ", STR_PAD_LEFT) . str_pad(str_replace(".", "", $vl_base_calculo), 11, "0", STR_PAD_LEFT) .
                    str_pad($branco, 10, " ", STR_PAD_LEFT) . str_pad($cod_ser_docto, 5, " ", STR_PAD_LEFT) . str_pad($cod_tit_acr, 16, " ", STR_PAD_LEFT) . str_pad($cdn_cliente, 14, " ") . PHP_EOL;
            fwrite($arquivo, $registro200);

            //300
            $val_aprop_ctbl = $val_tit_acr;
            $log_impto_val_agreg = 'N';
            $cod_plano_cta_ctbl = 'ANS2016';
            $cod_cta_ctbl = '91111911111110001';
            $cod_unid_negoc = 'PLS';
            $cod_tip_fluxo_financ = '1.1.1.10.20';

            $registro300 = '300' . str_pad($brancos, 63, " ", STR_PAD_LEFT) . str_pad(str_replace(".", "", $val_aprop_ctbl), 11, "0", STR_PAD_LEFT) . str_pad($brancos, 3, " ", STR_PAD_LEFT) .
                    $log_impto_val_agreg . str_pad($cod_plano_cta_ctbl, 8, " ", STR_PAD_LEFT) . str_pad($cod_cta_ctbl, 20, " ", STR_PAD_LEFT) . str_pad($cod_unid_negoc, 3, " ", STR_PAD_LEFT) .
                    str_pad($cod_tip_fluxo_financ, 12, " ", STR_PAD_LEFT) . PHP_EOL;

            fwrite($arquivo, $registro300);
        }
    }
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
}

echo '<script>alert("Arquivo gerado com Sucesso!")</script>';
echo '<script type="text/javascript">location.href = "layout_exportacoes.php";</script>';
