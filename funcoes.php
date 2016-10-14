<?php
 

function sanitizeString($string) {

    // matriz de entrada
    $what = array( 'ä','ã','à','á','â','ê','ë','è','é','ï','ì','í','ö','õ','ò','ó','ô','ü','ù','ú','û','À','Á','É','Í','Ó','Ú','ñ','Ñ','ç','Ç','Â°',';',':','|','!','"','#','$','%','&','/','=','?','~','^','>','<','ª','º','Ã§','Ã£','Ãµ','Ã©','Ã‰','Ã“','*','(',')','Â','°','ƒ');

    // matriz de saída
    $by   = array( 'a','a','a','a','a','e','e','e','e','i','i','i','o','o','o','o','o','u','u','u','u','A','A','E','I','O','U','n','n','c','C',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ','c','a','o','e','E','O',' ',' ',' ',' ',' ',' ');

    // devolver a string
    return str_replace($what, $by, $string);
}