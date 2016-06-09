<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "users".
 *
 * @property integer $id
 * @property string $name
 * @property string $password
 * @property string $access_token
 * @property string $auth_key
 * @property integer $port
 */
class Users extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'password', 'port','hosts_storage','sites_storage'], 'required'],
            [['port'], 'integer'],
            [['username', 'password'], 'string', 'max' => 60],
            [['hosts_storage','sites_storage'], 'string'],
            [['username','port'], 'unique']
        ];
    }

    public function setPassword($password) {
        $this->password = Yii::$app->security->generatePasswordHash($password);
    }
    public function validatePassword($password) {
        return Yii::$app->security->validatePassword($password, $this->password);
        //return $this->password === md5($password);
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'password' => 'Password',
            'port' => 'Port',
            'hosts_storage' => 'Host storage directory',
            'sites_storage' => 'Sites storage directory',
        ];
    }
}
