<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Hosts */
/* @var $form yii\widgets\ActiveForm */
$this->title = 'Удалить пользователя';
$this->params['breadcrumbs'][] = $this->title;
?>
<?if(isset($error)):?>
    <div class="alert alert-danger" role="alert"><?=$error?></div>
<?endif;?>
<div class="hosts-form">
	<?php $form = ActiveForm::begin(['options' => ['class' => 'host-create']]); ?>
	    <div class="form-group field-host-mode">
			<label class="control-label" for="user-id">Выберете пользователя для удаления</label>
			<select id="user-id" class="form-control" name="Users[id]">
				<?foreach ($model as $key => $value) :?>
					<?if(Yii::$app->user->identity->username != $value->username):?>
						<option value="<?=$value->id?>"><?=$value->username?></option>
					<?endif;?>
				<?endforeach;?>
			</select>

			<div class="del-homedir margintop">
				<label><input id="del-homedir__checkbox" name="Users[delete_homedir]" value="1" type="checkbox"> Удалить домашнюю папку пользователя</label>
				<div class="alert alert-warning" style="display:none" role="alert">
					Необходимо осторожно подходить к данной операции, так как домашняя папка пользователя может содержать ресурсы, принадлежащие пользователю
				</div>
			</div>

		</div>
	    <div class="form-group">
	        <?= Html::submitButton('Удалить') ?>
	    </div>
    <?php ActiveForm::end(); ?>
</div>
