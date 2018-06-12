<?php

namespace newsoftsnc\builder\models;

class GeneratoreController extends CComponent{
	var $entitydef;
	var $entityname;
	var $tablename;
	var $modelname;
	var $ctlname;
	var $filename;
	VAR $result;
	
	var $code;
	
	function GeneratoreController($entitydef){
		$this->entitydef=$entitydef;
		$this->entityname=$entitydef[NOME];
		$this->tablename=$entitydef[TABLE];
		$this->modelname=ucfirst(strtolower($this->entityname));
		
		//$this->ctlname=$ctlname="_".ucfirst(strtolower($this->entityname))."Controller";
		$this->ctlname=$ctlname=ucfirst(strtolower($this->entityname))."Controller";
		//$this->ctlname=$ctlname="Ctl".strtolower($this->entityname)."Controller";
		$path=Yii::getPathOfAlias('application.controllers');
		$this->filename="$path/$ctlname.php";
	}
	
	function build(){
		$this->code=<<<code
<?php
class $this->ctlname extends ControllerNs{
	var \$nomeentita="$this->entityname";
	var \$modelname="$this->modelname";


	/*
	public function ActionIndex(){
		\$this->render("/ns/index");
	}
	*/
	
	
	$this->codeDbCriteria
	


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
	
	
	function getCodeDbCriteria($filtriacc=false){
		$code=<<<code
		
	public function getDbCriteria(){
		\$model=new $this->modelname;
		\$criteria=new CDbCriteria();
				
		foreach (\$model->Parents as \$nome=>\$parent){
			\$keyp=\$_GET[\$nome][sKey];
			\$akey=array();
		}
				
		return \$criteria;
	}
code;
	
		return '';
		return $code;
		
	}
	
	function write(){
		$filename=$this->filename;
		
		if(file_exists($filename)){
			$this->result="File NON scritto $filename esistente";
			return;
		}
		
		
		
		if(file_put_contents($filename, $this->code)){
			$this->result="File scritto $filename";
		}else{
			$this->result="File NON scritto $filename";
		}
	}
	
	
	
}