<?php
/* @var $this DefaultController */
/*
$this->breadcrumbs=array(
	$this->module->id,
);
?>
<h1><?php echo $this->uniqueId . '/' . $this->action->id; ?></h1>

<p>
This is the view content for action "<?php echo $this->action->id; ?>".
The action belongs to the controller "<?php echo get_class($this); ?>"
in the "<?php echo $this->module->id; ?>" module.
</p>
<p>
You may customize this page by editing <tt><?php echo __FILE__; ?></tt>
</p>
*/


$xml=Data2::getXml();

$aalias=$xml->xpath('/root/DB/ENTITA');


foreach($aalias as $alias){
	$generatore=new GeneratoreAr($alias);
	$generatore->build();
	$generatore->write();
	echo $alias[NOME]." ".$generatore->result,"<BR>";
}

foreach($aalias as $alias){
	$generatore=new GeneratoreAr($alias);
	$generatore->build();
	$generatore->write_ext();
	echo $alias[NOME]." ".$generatore->result,"<BR>";
}

/*
foreach($aalias as $alias){
	$generatore=new GeneratoreController($alias);
	$generatore->build();
	$generatore->write();
	echo $alias[NOME]." ".$generatore->result,"<BR>";
}
*/

/*
foreach($aalias as $alias){
	$generatore=new GeneratoreFormModel($alias);
	$generatore->build();
	$generatore->write();
	echo $alias[NOME]." ".$generatore->result,"<BR>";
}
*/

	

