<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use app\models\LoginForm;
use app\models\HelloForm;
use app\models\ContactForm;
use app\models\HostSystemOS;
use app\models\UserIdentity;
use app\models\ChangePasswordForm;
use app\models\UserForm;
use app\models\Hosts;
use app\models\HostsSearch;
use app\components\FolderViewWidget;

const DEFAULT_HOST = 'default';
const COPY_CREATE_TYPE = 'copy';
const EMPTY_CREATE_TYPE = 'empty';

class SiteController extends Controller
{
    public function beforeAction($action)
    {
        if(!\Yii::$app->getUser()->isGuest) {
            if(UserIdentity::findByUsername(Yii::$app->user->identity->username)->port != $_SERVER["SERVER_PORT"]) {
                $this->actionLogout();
                return true;
            }
        }
        if($_SERVER["SERVER_PORT"] == "80") {
            if(\Yii::$app->getRequest()->url !== "/hello") {
                return $this->redirect('hello');
            }
            else return true;
        }
        elseif(\Yii::$app->getUser()->isGuest && (\Yii::$app->getRequest()->url !== Url::to(\Yii::$app->getUser()->loginUrl))) {
            return $this->redirect(\Yii::$app->getUser()->loginUrl);
        }
        else return true;
    }
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex($id = DEFAULT_HOST)
    {
        $hosts = new Hosts();
        $hostList = array();

        if($id !== DEFAULT_HOST) {
            $host = $hosts
                ->find()
                ->where([
                    "user_id" => \Yii::$app->getUser()->getId(),
                    "id" => $id
                    ])
                ->one();
        }
        else {
            $host = $hosts
                ->find()
                ->where([
                    "user_id" => \Yii::$app->getUser()->getId()
                    ])
                ->one();
        }

        if(!\Yii::$app->getUser()->isGuest) {
            if(Yii::$app->user->identity->is_admin) {
                if($id !== DEFAULT_HOST) {
                    $host = $hosts
                        ->find()
                        ->where([
                            "id" => $id
                            ])
                        ->one();
                }
                else {
                    $host = $hosts
                        ->find()
                        ->one();
                }

                $hostList = $hosts
                    ->find()
                    ->all();

                if(count($hostList)) {
                    $user = new UserIdentity();
                    $users = $user->find()->select(["id","username"])->all();
                    $user_list = array();
                    foreach ($users as $key => $usr) {
                        $user_list[$usr->id] = $usr->username;
                    }

                    return $this->render('index', [
                        'host' => $host,
                        'hostList' => $hostList,
                        'is_admin' => true,
                        'user_list' => $user_list
                    ]);
                }
            }
            else {
                $hostList = $hosts
                ->find()
                ->where([
                    "user_id" => \Yii::$app->getUser()->getId()
                    ])
                ->all();
            }

            if(empty($hostList)) {
                return $this->redirect('create');
            }
        }
        if(!$host) {
            return $this->render('error',[
                'name' => "Ошибка отображения хоста",
                'message' => "Такого хоста не существует для данного пользователя"
            ]);
        }
            
        return $this->render('index', [
            'host' => $host,
            'hostList' => $hostList,
        ]);
    }

    public function actionLogin()
    {
        $this->layout = "hellopage";

        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $user = UserIdentity::findIdentityByPort($_SERVER["SERVER_PORT"]);
        return $this->render('login', [
            'model' => $model,
            'username' => (isset($user))? $user->username : null
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Creates a new Hosts model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Hosts();
        $user = new UserIdentity();

        $post = Yii::$app->request->post();

        if(isset($post['Hosts'])) {
            $model->load(Yii::$app->request->post("Hosts"));

            if($post['Hosts']['create_type'] == COPY_CREATE_TYPE) {
                $hosts = new Hosts();
                $srcHost = $hosts->find()->where(['id' => $post['Hosts']['host_id']])->one();

                if($model->createHost($post['Hosts']['mode'],$srcHost->home_dir)) {
                    return $this->redirect(["index",'id' => $model->id]);
                }
            }
            elseif($post['Hosts']['create_type'] == EMPTY_CREATE_TYPE) {
                if($model->createHost($post['Hosts']['mode'])) {
                    return $this->redirect(["index",'id' => $model->id]);
                }
            }

            
        }

        $hostList = $model
                ->find()
                ->all();

        return $this->render('create', [
                'model' => $model,
                'hostList' => $hostList
            ]);
    }

    public function actionDelete($id)
    {
        $model = Hosts::findOne($id);
        $user = Yii::$app->user->identity;

        if($model) {
            if($user->is_admin) {
                $host_owner = UserIdentity::findIdentity($model->user_id);
                $k = HostSystemOS::deleteHost($model->home_dir, $host_owner->hosts_storage."/".$model->name.".conf");
                $model->delete();
            }
            else {
                if($model->user_id == $user->id) {
                    if(!empty($model)) {
                        HostSystemOS::removeFolder($model->home_dir);
                        unlink(Yii::$app->user->identity->hosts_storage."/".$model->name.".conf");
                        $model->delete();
                    }
                }
                else {
                    return $this->render('error',[
                        'name' => "Ошибка удаления хоста",
                        'message' => "Такого хоста не существует или у Вас нет прав на его удаление"
                    ]);
                }
            }
        }
        else {
            return $this->render('error',[
                'name' => "Ошибка удаления хоста",
                'message' => "Такого хоста не существует или у Вас нет прав на его удаление"
            ]);
        }
        

        HostSystemOS::reloadApache();

        return $this->redirect(['index']);
    }

    public function actionViewHost($id) {
        $model = new Hosts();
        $host = $model
                ->find()
                ->where([
                    "id" => $id
                    ])
                ->one();
        $folder = (isset($host))? $host->home_dir : null;
        return FolderViewWidget::widget([
            "folder" => $folder
        ]);
    }

    public function actionChangePassword() {
        $model = new ChangePasswordForm();
        
        if($model->load(Yii::$app->request->post()) && $model->validate()) {
            if($model->changePassword()) {
                return $this->redirect('index');
            }
        }

        return $this->render('change_password', [
            'model' => $model,
        ]);
    }

    public function actionCreateUser() {
        if(!Yii::$app->user->identity->is_admin) {
            //return $this->redirect('index');
            return $this->render('error',[
                'name' => "Ошибка доступа",
                'message' => "У Вас нет прав на создание пользователей"
            ]);
        }
        $model = new UserForm;
        
        if($model->load(Yii::$app->request->post()) && $model->validate()) {
            if($model->createUser()) {
                return $this->redirect('index');
            }
        }

        return $this->render('user_create', [
            'model' => $model,
        ]);
    }
    public function actionDeleteUser() {
        if(!Yii::$app->user->identity->is_admin) {
            //return $this->redirect('index');
            return $this->render('error',[
                'name' => "Ошибка доступа",
                'message' => "У Вас нет прав на удаление пользователей"
            ]);
        }
        $users = new UserIdentity;
        $hosts = new Hosts();

        $model = $users->find()->select(['id','username'])->all();


        if(Yii::$app->request->post("Users")["id"]) {
            $k = Yii::$app->request->post("Users");

            $host = $hosts
                ->find()
                ->where([
                    "user_id" => $k["id"]
                    ])
                ->select(["id"])
                ->all();
            if(count($host)) {
                return $this->render('user_delete', [
                    'model' => $model,
                    'error' => "Пользователь, которого Вы пытаетесь удалить имеет активные хосты. Необходимо сначала удалить их."
                ]);
            }

            if(Yii::$app->user->identity->id == $k["id"]) {
                return $this->render('user_delete', [
                    'model' => $model,
                    'error' => "Пользователь не может удалить сам себя"
                ]);
            }
            
            $user = $users->find()->where(["id" => $k["id"]])->one();
            if($user) {
                if(!isset($k["delete_homedir"])) $k["delete_homedir"] = false;
                HostSystemOS::deleteUser($user->username, $user->port, $k["delete_homedir"]);
                $user->delete();
            }

            return $this->redirect('index');
        }

        return $this->render('user_delete', [
            'model' => $model
        ]);
    }

    /*public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }*/

    /*public function actionAbout()
    {
        return $this->render('about');
    }*/
    public function actionHello()
    {
        $this->layout = "hellopage";
        $model = new HelloForm();

        if($model->load(Yii::$app->request->post()) && $model->check()) {
            $port = $model->port;
            $host = $_SERVER["HTTP_HOST"];
            return $this->redirect("http://$host:$port/login");
        }
        		
        return $this->render('hello',["model" => $model]);
    }
}
