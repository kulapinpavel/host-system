<?
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
<h1>Введите имя пользователя:</h1>
<?php $form = ActiveForm::begin(); ?>
<div class="row">
	<div class="col-md-6">
		<?=$form->field($model,"username")->textInput(); ?>
	</div>
	<div class="col-md-12">
		<?=Html::submitButton('Войти',["class" => "btn btn_success"]); ?>
	</div>
</div>
<?php ActiveForm::end(); ?>