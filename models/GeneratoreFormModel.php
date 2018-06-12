<?php

namespace newsoftsnc\builder\models;

class GeneratoreFormModel extends CComponent{
	var $entitydef;
	var $entityname;
	var $tablename;
	var $modelname;
	var $filename;
	var $result;
	
	var $code;
	
	function GeneratoreFormModel($entitydef){
		$this->entitydef=$entitydef;
		$this->entityname=$entitydef[NOME];
		$this->tablename=$entitydef[TABLE];
		
		$this->modelname=$modelname=ucfirst(strtolower($this->entityname))."Form";
		$path=Yii::getPathOfAlias('application.models');
		$this->filename="$path/$modelname.php";
	}
	
	function build(){
		$this->code=<<<code
<?php
class $this->modelname extends CFormModel{
$this->CodeVars
	
$this->CodeAttributeLabels

$this->CodeRules

}
code;
		
	}

	function getCodeRules(){
		$code=<<<code
		
	function rules(){
		return array();
	}
code;
		return $code;
	}
	
	function getCodeVars(){
		$aparenti=Dbfn::getParenti($this->entitydef);
		$aparent=Dbfn::getParent($this->entitydef);
		$allentity=array_merge($aparenti,$aparent,array($this->entitydef));
		$code='';
		foreach($allentity as $entity){
			$nometable=$entity[TABLE];
			$afield=$entity->xpath("/root/DB/TABLE[@NOME='$nometable']/FIELD");
			foreach($afield as $field){
				$nomevar=$entity[NOME].$field[NOME];
				$code.="\tvar \$$nomevar;\t\t\t //$field[DESCRIZIONE]\r\n";
			}
		}
		
		return $code;		
	}
	
	function getCodeAttributeLabels(){
		$aparenti=Dbfn::getParenti($this->entitydef);
		$aparent=Dbfn::getParent($this->entitydef);
		$allentity=array_merge($aparenti,$aparent,array($this->entitydef));
		$code="\tfunction attributeLabels(){\r\n";
		$code.="\t\treturn array(\r\n";
		foreach($allentity as $entity){
			$nometable=$entity[TABLE];
			$afield=$entity->xpath("/root/DB/TABLE[@NOME='$nometable']/FIELD");
			foreach($afield as $field){
				$caption=addslashes($field[CAPTION]);
				$nomevar=$entity[NOME].$field[NOME];
				$code.="\t\t\t'$nomevar'=>'$caption',\r\n";
			}
		}
		$code.="\t\t);\r\n\t}\r\n";
	
		return $code;
	}
	
	
	
	function write(){
		$filename=$this->filename;
		if(file_put_contents($filename, $this->code)){
			$this->result="File scritto $filename";
		}else{
			$this->result="File NON scritto $filename";
		}
	}
	
	
	
	
}