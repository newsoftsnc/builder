<?php
$xml=Data2::getXml();

$aalias=$xml->xpath('/root/DB/ENTITA');


foreach($aalias as $alias){
	$generatore=new GeneratoreController($alias);
	$generatore->build();
	//echo $generatore->filename,"<br>";
	$generatore->write();
	
	echo $alias[NOME]." ".$generatore->result,"<BR>";
}

