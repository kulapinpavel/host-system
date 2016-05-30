<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\HostSystemOS;
use app\models\UserIdentity;

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

    public function actionIndex()
    {
        return $this->render('index');
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

    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    public function actionAbout()
    {
        return $this->render('about');
    }
    public function actionHello()
    {
        $this->layout = "hellopage";
        $model = new UserIdentity();

        if(isset($_POST['UserIdentity'])) {
            $port = UserIdentity::findByUsername($_POST['UserIdentity']["username"])->port;
            
            return $this->redirect("http://hostsystem:$port/login");
        }
        		
        return $this->render('hello',["model" => $model]);
    }
}
