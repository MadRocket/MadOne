<?php

//require_once("load_font.php");
/*
error_reporting(0);

echo ord('ô') . '<br>'; // 244
echo chr(246);

return; */

require_once("dompdf_config.inc.php");

$html = implode('', file('list4.html'));
//$html = iconv("ISO-8859-1", "UTF-8", $html);



$dompdf = new DOMPDF();
$dompdf->set_paper(array(0,0,990,1400), 'landscape');

$dompdf->load_html($html);

//$dompdf->selectFont('./fonts/Helvetica.afm',
//        array('encoding'=>'WinAnsiEncoding'));

$dompdf->render();
$dompdf->stream("sample.pdf", array("Attachment" => 0));

?>