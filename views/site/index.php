<?php
use app\components\FolderViewWidget;
use yii\bootstrap\Dropdown;
/* @var $this yii\web\View */

$this->title = 'Hostsystem control page';
?>
<?
$hosts = array();
foreach ($hostList as $key => $value){
	if($value->id == $host->id) continue;

	$hosts[] = array("label" => $value->name, "url" => $value->id);
}
?>
<?php if (!\Yii::$app->getUser()->isGuest): ?>
	<div class="row">
		<div class="col-md-6">
			<div class="host">
				<?if(isset($host)):?>
					<h3>Хост</h3>
					<div class="dropdown">
						<button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
							<?=$host->name;?>
							<span class="caret"></span>
						</button>
						<a href="delete/<?=$host->id?>" class="button-delete" title="Удалить хост"><span class="glyphicon glyphicon-minus-sign"></span></a>
						<a href="create" class="button-create" title="Создать хост"><span class="glyphicon glyphicon-plus-sign"></span></a>
						<?php
					        echo Dropdown::widget([
					            'items' => $hosts,
					        ]);
					    ?>
					</div>

					<?=FolderViewWidget::widget([
						"folder" => $host->home_dir
					]);?>
				<?else:?>
					<p>Хоста с таким id не существует для данного пользователя</p>	
				<?endif;?>
			</div>	
		</div>
	</div>
<?php endif ?>
