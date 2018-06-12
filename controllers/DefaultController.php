<?php

namespace newsoftsnc\builder\controllers;

use Yii;
use yii\web\Controller;

class DefaultController extends Controller
{
	public function actionIndex()
	{
		$this->render('index');
	}
	public function actionBuildModels()
	{
		$this->render('bldmodels');
	}
	public function actionBuildcontroller()
	{
		$this->render('bldcontroller');
	}
	
	
}