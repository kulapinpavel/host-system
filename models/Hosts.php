<?php

namespace app\models;

use Yii;
use app\models\UserIdentity;
use app\models\HostSystemOS;

/**
 * This is the model class for table "hosts".
 *
 * @property integer $id
 * @property string $name
 * @property string $home_dir
 * @property integer $user_id
 *
 * @property Users $user
 */
class Hosts extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'hosts';
    }

    public function load( $data, $formName = null ) {
        $user = UserIdentity::findByUsername(Yii::$app->user->identity->username);

        $this->name = $data["name"];
        $this->home_dir = $data["home_dir"];
        $this->user_id = $user->id;

        if($this->name && $this->home_dir && $this->user_id && $this->validate())
            return true;
        else
            return false;
    }
    public function createHost($mode,$srcFolder = null) {
        if($this->name && $this->home_dir && $this->user_id && $this->validate()) {
            $user = UserIdentity::findByUsername(Yii::$app->user->identity->username);
            try {
                $vhost = HostSystemOS::addVHost(
                    $user->hosts_storage."/".$this->name.".conf",
                    $this->name,
                    'mail@mail.ru',
                    '80',
                    $this->home_dir,
                    $user->username,
                    'hostsystem'
                );
            }
            catch(\Exception $e) {
                $this->addError('name',"Невозможно создать хост с таким именем");
                return false;
            }
            switch ($mode) {
                case '750':
                    $mode = 0750;
                    break;
                case '770':
                    $mode = 0770;
                    break;
                case '700':
                    $mode = 0700;
                    break;
                default:
                    $mode = 0755;
                    break;
            }
            if(isset($srcFolder)) {
                try {
                    $homedir = HostSystemOS::copyFolder($srcFolder, $this->home_dir);
                }
                catch(\Exception $e) {
                    unlink($user->hosts_storage."/".$this->name.".conf");
                    $homedir = $this->home_dir;
                    $this->addError('home_dir',"Невозможно создать каталог с таким именем $srcFolder $homedir");
                    return false;
                }

                HostSystemOS::reloadApache();

                if($homedir && $vhost && $this->save()) {
                    return true;
                }
                else return false;
            }
            else {
                try {
                    $homedir = mkdir($this->home_dir, $mode);
                }
                catch(\Exception $e) {
                    unlink($user->hosts_storage."/".$this->name.".conf");
                    $this->addError('home_dir',"Невозможно создать каталог с таким именем");
                    return false;
                }
                                
                $html = "<!DOCTYPE html><html><head><title>It works!</title></head><body><h1>It works!</h1></body></html>";

                try {
                    $fp = fopen($this->home_dir."/index.html", 'aw');
                    $filewrite = fwrite($fp, $html);
                    fclose($fp);
                }
                catch(\Exception $e) {
                    unlink($user->hosts_storage."/".$this->name.".conf");
                    rmdir($this->home_dir);
                    $this->addError('home_dir',"Запись в каталог невозможна");
                    return false;
                }

                HostSystemOS::reloadApache();

                if($homedir && $vhost && $filewrite && $this->save()) {
                    return true;
                }
                else return false;
            }
        }
        else return false;
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'home_dir', 'user_id'], 'required', 'message' => '{attribute} не может быть пустым'],
            [['name','home_dir'],'unique','message' => '{attribute} уже существует'],
            [['home_dir'], 'string'],
            [['user_id'], 'integer'],
            [['name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Идентификатор хоста',
            'name' => 'Имя хоста',
            'home_dir' => 'Домашний каталог хоста',
            'user_id' => 'Идентификатор пользователя владельца хоста',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }
}
