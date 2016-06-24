<?php
namespace app\models;

use app\models\UserIdentity;
use app\models\HostSystemOS;
use Yii\base\model;
use Yii;

class UserForm extends Model {
	public $username;
	public $password;
	public $password_confirm;
	public $is_admin;

	public function rules()
    {
        return [
            [['username', 'password', 'password_confirm'], 'required', 'message' => '{attribute} не может быть пустым'],
            [['username', 'password', 'password_confirm'], 'string'],
            [['username'], 'checkUser'],
            [['is_admin'],'integer'],
            [['username'], 'unique', 'targetClass' => '\app\models\Users','message' => '{attribute} должно быть уникальным'],
            ['password_confirm', 'compare', 'compareAttribute' => 'password','message' => '{attribute} должно совпадать с введённым паролем']
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => 'Имя пользователя',
            'password' => 'Пароль',
            'password_confirm' => 'Подтверждение пароля',
            'is_admin' => 'Администратор'
        ];
    }
    public function checkUser($attribute) {
        $users = HostSystemOS::getUsers();
        if (in_array($this->username, $users)) {
            $this->addError($attribute, 'Пользователь с таким именем уже существует в операционной системе.');
        }
    }
    public function createUser($host_storage_dir = "/apache2/sites-enabled", $sites_storage_dir = "/www") {
    	if($this->validate()) {
    		$max_port = 0;
    		$user = new UserIdentity;

    		$ports = $user->find()->select('port')->all();
    		
    		foreach($ports as $port) {
    			if($port->port > $max_port) {
    				$max_port = $port->port;
    			}
    		}

    		$user->username = $this->username;
    		$user->setPassword($this->password);
    		$user->port = ++$max_port;
    		$user->hosts_storage = "/home/".$this->username.$host_storage_dir;
    		$user->sites_storage = "/home/".$this->username.$sites_storage_dir;
    		$user->generateAuthKey();
    		if($this->is_admin) {
    			$user->is_admin = IntVal($this->is_admin);
    		}
    		else {
    			$user->is_admin = 0;
    		}
    		HostSystemOS::createUser($user->username, $this->password, $user->port, $user->is_admin == 1);    		

    		$user->save();

    		return $user;
    	}
    	return false;
    }
}