<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Hosts */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="hosts-form">
	<?php $form = ActiveForm::begin(['options' => ['class' => 'host-create']]); ?>
	    <div class="form-group field-host-mode">
			<label class="control-label" for="user-id">Выберете пользователя для удаления</label>
			<select id="user-id" class="form-control" name="Users[id]">
				<?foreach ($model as $key => $value) :?>
					<option value="<?=$value->id?>"><?=$value->username?></option>
				<?endforeach;?>
			</select>
		</div>
	    <div class="form-group">
	        <?= Html::submitButton('Удалить') ?>
	    </div>
    <?php ActiveForm::end(); ?>
</div>
