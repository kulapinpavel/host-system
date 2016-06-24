<?php
use app\components\FolderViewWidget;
use yii\bootstrap\Dropdown;
/* @var $this yii\web\View */

$this->title = 'Hostsystem control page';
?>
<?
$hosts = array();
if(isset($host)) {
	if(isset($is_admin)) {
	foreach ($hostList as $key => $value){
		if($value->id == $host->id) continue;

		$hosts[] = array("label" => $value->name.' ('.$user_list[$value->user_id].")", "url" => $value->id);
	}
	}
	else {
		foreach ($hostList as $key => $value){
			if($value->id == $host->id) continue;

			$hosts[] = array("label" => $value->name, "url" => $value->id);
		}
	}
}
?>
<?php if (!\Yii::$app->getUser()->isGuest): ?>
	<?if(isset($host)):?>
		<div class="row">
			<div class="col-md-12">
				<h3>Хост</h3>
				<div class="dropdown">
					<button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
						<?if(isset($is_admin)):?>
							<?=$host->name.' ('.$user_list[$host->user_id].")";?>
						<?else:?>
							<?=$host->name;?>
						<?endif;?>
						<span class="caret"></span>
					</button>
					<a href="delete/<?=$host->id?>" class="button-delete" title="Удалить хост" onclick="return confirm('Вы действительно хотите удалить этот хост?');"><span class="glyphicon glyphicon-minus-sign"></span></a>
					<a href="create" class="button-create" title="Создать хост"><span class="glyphicon glyphicon-plus-sign"></span></a>
					<?php
				        echo Dropdown::widget([
				            'items' => $hosts,
				        ]);
				    ?>
				</div>
			</div>
		</div>
		<div class="row margintop">
			<div class="col-md-6">
				<div class="host">
						<?=FolderViewWidget::widget([
							"folder" => $host->home_dir
						]);?>
					
				</div>	
			</div>
		</div>
		<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="exampleModalLabel">New message</h4>
					</div>
					<div class="modal-body"></div>
				</div>
			</div>
		</div>
	<?else:?>
		<div class="alert alert-danger" role="alert">Хоста с таким id не существует для данного пользователя</div>
	<?endif;?>
<?php endif ?>
