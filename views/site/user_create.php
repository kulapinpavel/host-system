<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Hosts */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="hosts-form">
    <?php $form = ActiveForm::begin(['options' => ['class' => 'user-create']]); ?>

	    <?= $form->field($model, 'username')->textInput() ?>
	    <?= $form->field($model, 'password')->passwordInput() ?>
	    <?= $form->field($model, 'password_confirm')->passwordInput() ?> 	
	    <?= $form->field($model, 'is_admin')->checkbox() ?> 	

	    <div class="form-group">
	        <?= Html::submitButton('Создать') ?>
	    </div>

    <?php ActiveForm::end(); ?>
    <pre><?var_dump($dump)?></pre>
</div>
