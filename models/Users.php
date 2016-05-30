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
            [['name', 'password', 'access_token', 'auth_key', 'port'], 'required'],
            [['port'], 'integer'],
            [['name', 'password', 'access_token', 'auth_key'], 'string', 'max' => 32],
            [['id', 'name','port'], 'unique']
        ];
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
            'access_token' => 'Access Token',
            'auth_key' => 'Auth Key',
            'port' => 'Port',
        ];
    }
}
