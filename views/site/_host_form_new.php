<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

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

	    <?= $form->field($model, 'name')->textInput(['maxlength' => true,'id'=>'hosts-name-1']) ?>
	    <?= $form->field($model, 'home_dir',
		    ['template'=>'{label}<div class="input-group"><span class="input-group-addon"><input type="checkbox" class="directory-switcher" title="Указать каталог вручную"></span>{input}</div>{error}'])
		    ->textInput([
		    	'readonly' => 'true',
		    	'id'=>'hosts-home_dir-1',
		    	'value' => $sites_storage,
		    	'data-homedir' => $sites_storage,
		    	]) ?>

		<div class="form-group field-host-mode">
			<label class="control-label" for="host-mode-1">Модификатор доступа</label>
			<select id="host-mode-1" class="form-control" name="Hosts[mode]">
					<option value="750">Разрешить пользователям системы просматривать файлы</option>
					<option value="770">Разрешить пользователям системы просматривать и изменять файлы</option>
					<option value="700">Запретить доступ всем кроме меня</option>
			</select>
		</div>    	

	    <input type="hidden" name="Hosts[create_type]" value="empty">

	    <div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Обновить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>

    <?php ActiveForm::end(); ?>
</div>
