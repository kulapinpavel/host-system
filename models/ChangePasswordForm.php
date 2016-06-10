<?php
namespace app\models;

use app\models\UserIdentity;
use Yii\base\model;
use Yii;

class ChangePasswordForm extends Model {
	public $old_password;
	public $password;
	public $password_confirm;

	public function rules()
    {
        return [
            [['old_password', 'password', 'password_confirm'], 'required', 'message' => '{attribute} не может быть пустым'],
            [['old_password', 'password', 'password_confirm'], 'string'],
            [['old_password'], 'validatePassword'],
            ['password_confirm', 'compare', 'compareAttribute' => 'password','message' => '{attribute} должно совпадать с введённым cтарым паролем']
        ];
    }

    public function attributeLabels()
    {
        return [
            'old_password' => 'Старый пароль',
            'password' => 'Новый пароль',
            'password_confirm' => 'Подтверждение нового пароля'
        ];
    }

    public function changePassword() {
    	$model = new UserIdentity();
        $user = $model->find()->where(['id' => Yii::$app->user->identity->id])->one();

        $user->setPassword($this->password);
        if($user->save()) return true;
        return false;
    }
    public function validatePassword($attribute) {
        if(!Yii::$app->user->identity->validatePassword($this->old_password)) {
            $this->addError($attribute,"Cтарый пароль не верный");
        }
    }
}