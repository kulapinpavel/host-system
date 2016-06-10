<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Hosts */
/* @var $form yii\widgets\ActiveForm */
$this->title = 'Изменить пароль';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="hosts-form">
    <?php $form = ActiveForm::begin(['options' => ['class' => 'user-create']]); ?>

	    <?= $form->field($model, 'old_password')->passwordInput() ?>
	    <?= $form->field($model, 'password')->passwordInput() ?>
	    <?= $form->field($model, 'password_confirm')->passwordInput() ?> 		

	    <div class="form-group">
	        <?= Html::submitButton('Изменить пароль') ?>
	    </div>

    <?php ActiveForm::end(); ?>
</div>
