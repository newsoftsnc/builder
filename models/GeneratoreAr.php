<?php

namespace newsoftsnc\builder\models;

class GeneratoreAr extends CComponent{
	var $entitydef;
	var $entityname;
	var $entitydescr;
	var $tablename;
	var $tabledef;
	var $modelname;
	var $file_name;
	var $filename;
	VAR $result;
	
	var $code;
	var $code_ext;
	
	function GeneratoreAr($entitydef){
		$this->entitydef=$entitydef;
		$this->entityname=$entitydef[NOME];
		$this->entitydescr=$entitydef[DESCRIZIONE];
		$this->tablename=$tablename=$entitydef[TABLE];
		
		$this->tabledef=$entitydef->xpath("/root/DB/TABLE[@NOME='$tablename']")[0];
		
		$this->modelname=$modelname=ucfirst(strtolower($this->entityname));
		$path=Yii::getPathOfAlias('application.models');
		$this->filename="$path/$modelname.php";
		$this->file_name="$path/_$modelname.php";
	}
	
	function build(){
		$this->code=<<<code
<?php
$this->CodeDocs
class _$this->modelname extends CActiveRecordNs{
	var \$nomeentita="$this->entityname";
	var \$descrizioneentita="$this->entitydescr";
	// var \$tableAlias="$this->modelname";
	
	public static function model(\$className=__CLASS__){
		return parent::model(\$className);
	}

	public function tableName(){
		return '$this->tablename';
	}
			
$this->CodeRules

$this->CodeRelations		

$this->CodeRelationP

$this->CodeGetLinks		
		
$this->CodeGetParents		

$this->CodeGetParent

$this->CodeGetParentsI		

$this->CodeGetChildNames

$this->CodeAttributeLabels		
		
$this->CodeGetCampi

$this->CodeGetCampiS2

$this->CodeGetCampiGrid

$this->CodeGetAliasFratelli		

$this->CodeGetEachParent

$this->CodeCalcola

public function defaultScope(){
	return ['alias'=>'$this->modelname'];
}
		
		
}
code;

	$this->code_ext=<<<code_ext
<?php
class $this->modelname extends _$this->modelname{
		
	public static function model(\$className=__CLASS__){
		return parent::model(\$className);
	}
		
}
code_ext;
		
	}

	function getCodeRules(){
		$afielddef=$this->tabledef->FIELD;
		
		$stringrequired="";
		$stringnumerical='';
		$stringdate='';
		
		$apk=[];
		$apfk=[];
		
		foreach($afielddef as $fielddef ){
			//IF($fielddef[PK]==1 OR $fielddef[PFK]==1 OR $fielddef[FK]==1 OR $fielddef[CALCOLATO]==1) CONTINUE;
			$stringsafe.=$fielddef[NOME].",";
			
			
//			IF($fielddef[PK]==1 and $fielddef[AI]==0){
			IF($fielddef[PK]==1){
// 				if($primok){$stringdupkey.=",";}
// 				$stringdupkey.=$fielddef[NOME];
				if($fielddef[AI]==0) $stringrequired.=$fielddef[NOME].",";
 				$primok=true;
				if(count($apk)==0){
					$apk[]="$fielddef[NOME]";
				}else{
					$apfk[]="$fielddef[NOME]";
				}
				
			}
			
			IF($fielddef[PFK]==1){
// 				if(!$primok and !$primo) $stringdupkey.=$fielddef[NOME];
// 				if($primo){$stringdupkeywith.=",";}
// 				$stringdupkeywith.=$fielddef[NOME];
 				$primo=true;
				$apfk[]="$fielddef[NOME]";
			}

			IF($fielddef[OBBLIGATORIO]==1){
				$stringrequired.=$fielddef[NOME].",";
			}
			
			IF($fielddef[TIPO]=='N'){
				$stringnumerical.=$fielddef[NOME].",";
			}
			
			IF($fielddef[TIPO]=='D'){
				$stringdate.=$fielddef[NOME].",";
			}
				
			
			$message=addslashes($this->entitydescr." presente");
			
		}
		
		$stringdupkey		=	implode(',', $apk);
		$stringdupkeywith	=	implode(',', $apfk);
		
		$strunique="array('$stringdupkey','UniqueAttributesValidator','with'=>'$stringdupkeywith','message'=>'$message'),";
		if(!$primo){
			//$stringdupkey='';
			if($primok){
				$strunique="array('$stringdupkey','unique','message'=>'$message'),";
			}
		}

		
		/*
		 * creo controlli indici unique
		 */
		
		/* DA RIVEDERE COMPLETAMENTE */
		
		$atableindex=$afielddef=$this->tabledef->INDEX;
		foreach ($atableindex as $tableindex){
			$unique="$tableindex[UNIQUE]";
			IF(!$unique) continue;
			$primakey=FALSE;
			if(count($tableindex->KEY)==1){
				$fieldkey=$tableindex->KEY[0][NOME];
				$strunique.="\r\n\t\t\tarray('$fieldkey','unique','message'=>'$tableindex[MESSAGE]'),";
			}else{
				$primakey=true;
				$akeys=[];
				foreach($tableindex->KEY as $key){
					if($primakey){
						$prima="$key[NOME]";
						$primakey=false;
						continue;
					}
					$akeys[]="$key[NOME]";
				}
				$stringdupkeywith=implode(',', $akeys);				
				$strunique.="\r\n\t\t\tarray('$prima','UniqueAttributesValidator','with'=>'$stringdupkeywith','message'=>'$tableindex[MESSAGE]'),";
			}
			
		}
		
		
		//array('id_materia', 'UniqueAttributesValidator', 'with'=>'id_classe,tipodocente', 'message'=>'Cattedra già inserita !!',),
		
		$code=<<<code
		
	function rules(){
		return array(
			array('$stringsafe','safe'),
			array('$stringrequired','required'),
			array('$stringnumerical','numerical'),
			//array('$stringdate','date'),
			$strunique
			//array('$stringdupkey','UniqueAttributesValidator','with'=>'$stringdupkeywith','message'=>'$message'),
			//array('$stringdupkeywith','required'),
			array('*','checkrelations'),
		);
	}
code;
		return $code;
	}
	
	function write(){
		$filename=$this->file_name;
		if(file_put_contents($filename, $this->code)){
			$this->result="File scritto $filename";
		}else{
			$this->result="File NON scritto $filename";
		}
	}

	function write_ext($overwrite=false){
		$filename=$this->filename;
		if((file_exists($filename) and $overwrite) or (!file_exists($filename))){
			if(file_put_contents($filename, $this->code_ext)){
				$this->result="File scritto $filename";
			}else{
				$this->result="File NON scritto $filename";
			}
		}
		if(file_exists($filename) and !$overwrite){
			$this->result="File NON scritto <b style=\"color:red\">$filename esistente</b>";
		}
		
	}
	
	function getCodeRelations(){
		$code ="\tpublic function relations(){\r\n";
		$code.="\t\treturn array(\r\n";
		$arelationp=$this->entitydef->LINKP;
		$arelationc=$this->entitydef->LINKC;
		
		foreach($arelationp as $RELP){
			$parentnome=ucfirst(strtolower($RELP[ALIAS]));
			
			$textkeys="";
			foreach($RELP->COPPIA as $COPPIA){
				$textkeys.="'$COPPIA[FIELDC]'=>'$COPPIA[FIELDP]',";
			}
			//$code.="\t\t\t'$parentnome'=>array(self::BELONGS_TO,'$parentnome',array($textkeys),'joinOptions'=>'FORCE INDEX (PRIMARY)'),\r\n";
			$code.="\t\t\t'$parentnome'=>array(self::BELONGS_TO,'$parentnome',array($textkeys)),\r\n";
		}

		foreach($arelationc as $RELC){
			$childnome=ucfirst(strtolower($RELC[ALIAS]));
				
			$textkeys="";
			foreach($RELC->COPPIA as $COPPIA){
				$textkeys.="'$COPPIA[FIELDC]'=>'$COPPIA[FIELDP]',";
			}
			$code.="\t\t\t'$childnome'=>array(self::HAS_MANY,'$childnome',array($textkeys)),\r\n";
		}
		
		$code.="\t\t);\r\n";
		$code.="\t}\r\n";
		
		return $code;
	}

	function getCodeRelationP(){
		$code ="\tpublic function getRelationP(){\r\n";
		$code.="\t\treturn array(\r\n";
		$arelation=$this->entitydef->xpath("LINKP");
		
		foreach($arelation as $RELP){
			$parentnome=ucfirst(strtolower($RELP[ALIAS]));
			$code.="\t\t\t'$parentnome'=>array('identificativa'=>$RELP[IDENTIFICATIVA],'obbligatoria'=>$RELP[OBBLIGATORIA]),\r\n";
		}

		$code.="\t\t);\r\n";
		$code.="\t}\r\n";
		
		return $code;
	}
	
	function GetCodeGetParents(){
		$code ="\tpublic function getParents(){\r\n";
		$code.="\t\treturn array(\r\n";
	
		$apath=Dbfn::getPath($this->entitydef);
		foreach($apath as $path){
			$cpath=implode("->",$path);
			$aliasname=end($path);
	
			$code.="\t\t\t'$aliasname'=>\$this->$cpath,\r\n";
		}
		$code.="\t\t);\r\n";
		$code.="\t}\r\n";
	
		return $code;
	}

	function GetCodeGetEachParent(){
		$code='';
		$anomip=[];
		
		$arelation=$this->entitydef->xpath("LINKP");
		foreach($arelation as $relation){
			$anomip[]=ucfirst(strtolower($relation[ALIAS]));
		}
		$apath=Dbfn::getPath($this->entitydef);
		foreach($apath as $path){
			$cpath=implode("->",$path);
			$aliasname=end($path);
			
			if(in_array($aliasname, $anomip)) continue;

			$code.="\tpublic function get$aliasname(){\r\n";
			$code.="\t\treturn \$this->$cpath;\r\n";
			$code.="\t}\r\n";
		}
	
		return $code;
	}
	
	function GetCodeGetParent(){
		$code ="\tpublic function getParent(){\r\n";
		$code.="\t\treturn array(\r\n";
	
		$aparent=Dbfn::getParent($this->entitydef);
		foreach($aparent as $parent){
			$aliasname=ucfirst(strtolower($parent[NOME]));
	
			$code.="\t\t\t'$aliasname',\r\n";
		}
		$code.="\t\t);\r\n";
		$code.="\t}\r\n";
	
		return $code;
	}

	function GetCodeGetChild(){
		$code ="\tpublic function getChild(){\r\n";
		$code.="\t\treturn array(\r\n";
	
		$achild=Dbfn::getChild($this->entitydef);
		foreach($achild as $child){
			$aliasname=ucfirst(strtolower($child[NOME]));
	
			$code.="\t\t\t'$aliasname',\r\n";
		}
		$code.="\t\t);\r\n";
		$code.="\t}\r\n";
	
		return $code;
	}
	
	function GetCodeGetParentsI(){
		$code ="\tpublic function getParentsI(){\r\n";
		$code.="\t\treturn array(\r\n";
	
		$aparent=Dbfn::getParenti($this->entitydef);
		
		foreach($aparent as $parent){
			$aliasname=ucfirst(strtolower($parent[NOME]));
	
			$code.="\t\t\t'$aliasname',\r\n";
		}
		$code.="\t\t);\r\n";
		$code.="\t}\r\n";
	
		return $code;
	}
	
	function GetCodeAfiltrip(){
		$code ="\tpublic function getAfiltrip(){\r\n";
		$code.="\t\treturn array(\r\n";
	
		$apath=Dbfn::getPath($this->entitydef);
		foreach($apath as $path){
			$cpath=implode("->",$path);
			$aliasname=end($path);
	
			$filtri='Yii::app()->user->getState(\'rules\')'."[$aliasname]";
			
			$code.="\t\t\t'$aliasname'=>$filtri,\r\n";
		}
		
		
		
		$code.="\t\t);\r\n";
		$code.="\t}\r\n";
	
		return $code;
	}

	
	function GetCodeGetLinks(){
		$code ="\tpublic function getLinks(){\r\n";
		$code.="\t\treturn array(\r\n";
		
		$apath=Dbfn::getPath($this->entitydef);
		foreach($apath as $path){
			$cpath=implode(".",$path);
		
			$code.="\t\t\t'$cpath',\r\n";
		}
		$code.="\t\t);\r\n";
		$code.="\t}\r\n";
		
		return $code;
	}


	function GetCodeAttributeLabels(){
		$code ="\tpublic function attributeLabels(){\r\n";
		$code.="\t\treturn array(\r\n";
	
		$fields=$this->tabledef->xpath('FIELD');
		
		foreach($fields as $field){
			$caption=addslashes($field[CAPTION]);
			$code.="\t\t\t'$field[NOME]'=>'$caption',\r\n";
		}
		$code.="\t\t);\r\n";
		$code.="\t}\r\n";
	
		return $code;
	}

	function GetCodeGetCampi(){
		$code ="\tpublic function getCampi(){\r\n";
		$code.="\t\treturn array(\r\n";
	
		$fields=$this->tabledef->xpath('FIELD[@NASCOSTO=0]');
	
		foreach($fields as $field){
			$code.="\t\t\t'$field[NOME]' => array(";
			$code.="'TIPO' => '$field[TIPO]',";
			$code.="'LEN' => '$field[LEN]',";
			$code.="'DEC' => '$field[DEC]',";
			$code.="'FK' => '$field[FK]',";
			$code.="'PK' => '$field[PK]',";
			$code.="'PFK' => '$field[PFK]',";
			$code.="'CALCOLATO' => '$field[CALCOLATO]',";
			$code.="'AGGIORNATO' => '$field[AGGIORNATO]',";
			$code.="'CAPTION' => '".addslashes($field[CAPTION])."',";
			$code.="'CAPTIONB' => '".addslashes($field[CAPTIONB])."',";
			$code.="'LEN' => '$field[LEN]',";
			$code.="'ISCOMBO' => '$field[ISCOMBO]',";
			$code.="'FIELDVAL' => array(";
				
			foreach ($field->FIELDVAL as $FIELDV) {
				$code.= "'$FIELDV[EVALUE]'=>'".addslashes($FIELDV[DESCRIZIONE])."',";
			}
			$code.="),";
				
			$code.="),\r\n";
		}
		$code.="\t\t);\r\n";
		$code.="\t}\r\n";
	
		return $code;
	}
	
	
	function GetCodeGetChildNames(){
		$code ="\tpublic function getChildNames(){\r\n";
		$code.="\t\treturn array(\r\n";
		
		$achild=Dbfn::getChild($this->entitydef);
		
		foreach($achild as $child){
			$aliasname=ucfirst(strtolower($child[NOME]));
		
			$code.="\t\t\t'$aliasname',\r\n";
		}
		$code.="\t\t);\r\n";
		$code.="\t}\r\n";
		
		return $code;
	}

	function GetCodeGetAliasFratelli(){
		$code ="\tpublic function GetAliasFratelli(){\r\n";
		$code.="\t\treturn array(\r\n";
	
		$asibl=Dbfn::getFratelli($this->entitydef);
	
		foreach($asibl as $sibl){
			$aliasname=ucfirst(strtolower($sibl[NOME]));
	
			$code.="\t\t\t'$aliasname',\r\n";
		}
		$code.="\t\t);\r\n";
		$code.="\t}\r\n";
	
		return $code;
	}
	
	
	
	function GetCodeGetCampiS2(){
		$code ="\tpublic function getCampiS2(){\r\n";
		$code.="\t\treturn array(\r\n";
	
		$fields=$this->tabledef->xpath('FIELD[@NASCOSTO=0 and (@PK=1 or @ISDESCR=1)]');
		
		foreach($fields as $field){
			$code.="\t\t\t'$field[NOME]' => \$this->$field[NOME],\r\n";
		}
		$code.="\t\t);\r\n";
		$code.="\t}\r\n";
	
		return $code;
	}
	
	function GetCodeGetCampiGrid(){
		$code ="\tpublic function getCampiGrid(){\r\n";
		$code.="\t\treturn array(\r\n";
		
		$fields=$this->tabledef->xpath('FIELD[@NASCOSTO=0 and (@PK=1 or @ISDESCR=1)]');
	
		foreach($fields as $field){
			$code.="\t\t\t'$field[NOME]' => \$this->$field[NOME],\r\n";
		}
		$code.="\t\t);\r\n";
		$code.="\t}\r\n";
	
		return $code;
	}

	function GetCodeCalcola(){
		$code ="\tpublic function calcola(){\r\n";
		$fields=$this->tabledef->xpath("FIELD[FIELDCLC]");
	
		foreach($fields as $field){
			$OK=FALSE;
			foreach ($field->FIELDCLC AS $fieldclc){
				if("$fieldclc[ESPRESSIONE]">'') $OK=TRUE;
			}
			IF(!$OK) continue;
			$code.="\t\t\t\$this->$field[NOME] = ";
			foreach ($field->FIELDCLC AS $fieldclc){
				$code.=$fieldclc[ESPRESSIONE].";\r\n"; 
			}
		}
		$code.="\t}\r\n";
	
		return $code;
	}
	
	
	FUNCTION getCodeDocs(){
		$code ="/**\r\n";
		
		$fields=$this->tabledef->xpath('FIELD');
		
		foreach($fields as $field){
			switch ($field[TIPO]){
				case 'C':
					$tipo='string';
					break;
				case 'D':
					$tipo='date';
					break;
				case 'L':
					$tipo='boolean';
					break;
				case 'N':
					$tipo='numeric';
					break;
				case 'M':
					$tipo='string';
					break;
			}
				
			$caption=addslashes($field[CAPTION]);
			$code.=" * @property  $tipo \$$field[NOME] $field[DESCRIZIONE] \r\n";
		}
		
		$aparent=Dbfn::getParent($this->entitydef);
		
		$apath=Dbfn::getPath($this->entitydef);
		foreach($apath as $path){
			$aliasname=end($path);
	
			$code.=" * @property  $aliasname \$$aliasname PARENT\r\n";
		}
		
		$arelationc=$this->entitydef->LINKC;
		foreach($arelationc as $relationc){
			$aliasname="$relationc[ALIAS]";
			$aliasname=ucfirst(strtolower($aliasname));
			$code.=" * @property  $aliasname \$$aliasname CHILD\r\n";
		}
		
		$code.=" */\r\n";
		return $code;
	}
	
	
	
}