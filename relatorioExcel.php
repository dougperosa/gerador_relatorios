<?php

set_time_limit(60000);

error_reporting(0);

ini_set("memory_limit","-1");

include './PHPExcel/PHPExcel.php';

$objPHPExcel = new PHPExcel();

// Definimos o estilo da fonte
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);

// Criamos as colunas

$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', date('d/m/Y'));

$letras = array(0 => 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');

for ($x = 0; $x < mysql_num_fields($resultset); $x++) {
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($letras[$x] . '2', mysql_field_name($resultset, $x));
}

$linha = 3;

while ($row = mysql_fetch_row($resultset)) {
    for ($x = 0; $x < mysql_num_fields($resultset); $x++) {
        //echo $row[$x];        
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($x, $linha, $row[$x]);
    }
    $linha++;
}

//$objPHPExcel->getActiveSheet()->setTitle($titulo);
// Cabeçalho do arquivo para ele baixar
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="' . $titulo . '.xls"');
header('Content-Transfer-Encoding: binary');
header('Cache-Control: max-age=0');
// Acessamos o 'Writer' para poder salvar o arquivo
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

// Salva diretamente no output, poderíamos mudar arqui para um nome de arquivo em um diretório ,caso não quisessemos jogar na tela
$objWriter->save('php://output');
$conexaounimed->desconecta();
