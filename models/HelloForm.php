<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\UserIdentity;

/**
 * LoginForm is the model behind the login form.
 */
class HelloForm extends Model
{
    public $username;
    public $port;

    private $_user = false;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['username'], 'required', 'message' => "Имя пользователя не должно быть пустым"],
            [['username'], 'checkUser']
        ];
    }
    public function attributeLabels()
    {
        return [
            'username' => 'Пользователь',
        ];
    }

    public function checkUser($attribute) {
    	if(!$this->hasErrors()) {
    		$user = UserIdentity::findByUsername($this->username);
    		if(!$user) {
    			$this->addError($attribute,"Такого пользователя не существует");
    		}
    		else {
    			$this->port = $user->port;
    		}
    	}
    }
    public function check() {
    	return $this->validate();
    }
}
