<?php

use yii\helpers\Html;
use yii\bootstrap\Tabs;

/* @var $this yii\web\View */
/* @var $model app\models\Hosts */

$this->title = 'Cоздать хост';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hosts-create">
    <h1><?= Html::encode($this->title) ?></h1>
    <?
    if(!empty($hostList)) {
    	echo Tabs::widget([
		    'items' => [
		        [
		            'label' => 'Пустой',
		            'content' => $this->render("_host_form_new",["model" => $model]),
		            'active' => (Yii::$app->request->post("Hosts")["create_type"] == 'empty')
		        ],
		        [
		            'label' => 'Копия существующего хоста',
		            'content' => $this->render("_host_form_copy",["model" => $model,'hostList' => $hostList]),
		            'active' => (Yii::$app->request->post("Hosts")["create_type"] == 'copy')
		        ],
		    ],
		]);
    }
    else {
    	echo Tabs::widget([
		    'items' => [
		        [
		            'label' => 'Пустой',
		            'content' => $this->render("_host_form_new",["model" => $model]),
		            'active' => true
		        ],
		    ],
		]);
    }
    
	?>
</div>
