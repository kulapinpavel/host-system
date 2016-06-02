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
            [['username', 'password', 'access_token', 'auth_key', 'port','hosts_storage','sites_storage'], 'required'],
            [['port'], 'integer'],
            [['username', 'password', 'access_token', 'auth_key'], 'string', 'max' => 32],
            [['hosts_storage','sites_storage'], 'string'],
            [['id', 'username','port'], 'unique']
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
            'hosts_storage' => 'Host storage directory',
            'sites_storage' => 'Sites storage directory',
        ];
    }
}
