<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\FolderViewWidget;

/* @var $this yii\web\View */
/* @var $model app\models\Hosts */
/* @var $form yii\widgets\ActiveForm */

if(!\Yii::$app->getUser()->isGuest) {
	$sites_storage = Yii::$app->user->identity->sites_storage;
}
else {
	$sites_storage = "";
}
?>
<div class="hosts-form">
    <?php $form = ActiveForm::begin(['options' => ['class' => 'host-create']]); ?>
    <div class="row">
    	<div class="col-md-6">
		    <?= $form->field($model, 'name')->textInput(['maxlength' => true,'id'=>'hosts-name-2']); ?>
		    <?= $form->field($model, 'home_dir',
		    ['template'=>'{label}<div class="input-group"><span class="input-group-addon"><input type="checkbox" class="directory-switcher" title="Указать каталог вручную"></span>{input}</div>{error}'])
		    ->textInput([
		    	'readonly' => 'true',
		    	'id'=>'hosts-home_dir-2',
		    	'value' => $sites_storage,
		    	'data-homedir' => $sites_storage,
		    	]) ?>

		    <div class="form-group field-host-mode">
				<label class="control-label" for="host-mode-2">Модификатор доступа</label>
				<select id="host-mode-2" class="form-control" name="Hosts[mode]">
						<option value="750">Разрешить пользователям системы просматривать файлы</option>
						<option value="770">Разрешить пользователям системы просматривать и изменять файлы</option>
						<option value="700">Запретить доступ всем кроме меня</option>
				</select>
			</div>
		    <input type="hidden" name="Hosts[create_type]" value="copy">

		    <div class="form-group">
		        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Обновить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
		    </div>
	    </div>
	    <div class="col-md-6">
	    	<div class="form-group field-hostselect">
				<label class="control-label" for="hostselect">Хост для копирования</label>
				<select id="hostselect" class="form-control" name="Hosts[host_id]">
					<?foreach ($hostList as $key => $value):?> 
						<option value="<?=$value->id;?>"><?=$value->name;?></option>
					<?endforeach;?>	
				</select>
			</div>
			<div class="folderview"><?=FolderViewWidget::widget(["folder" => $hostList[0]->home_dir]);?></div>
	    </div>
	<div>
    <?php ActiveForm::end(); ?>
</div>
<?php 
$script = <<< JS

$("#hostselect").on('change',function() {
	var url = 'view-host/'+$(this).val();
	var selectData = {
		is_ajax: true
	};

	$.ajax({
        type: "POST",
        data: selectData,
        contentType: false,
        processData: false,
        url: url,
        dataType: "html",
        success: function (response) {
            $(".folderview").html(response);
            $('.folder-view > ul').tree({
				expanded: 'li:first'
			});
        },
        beforeSend: function () {
        },
        complete: function () {
        }
    });
});

JS;
$this->registerJS($script);
?>