<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\bootstrap\Dropdown;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);

$available_actions = [
    '<li class="dropdown-header">Доступные действия</li>',
    '<li class="divider"></li>',
];
if(isset(Yii::$app->user->identity)){
    if(Yii::$app->user->identity->is_admin) {
        $available_actions[] = ['label' => 'Создать пользователя', 'url' => 'create-user'];
        $available_actions[] = ['label' => 'Удалить пользователя', 'url' => 'delete-user'];
    }
}
$available_actions[] = ['label' => 'Изменить пароль', 'url' => 'change-password'];
$available_actions[] = ['label' => 'Выйти', 'url' => 'logout'];

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => 'Система управления хостингом',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => [
            ['label' => 'Обзор хостов', 'url' => ['/site/index']],
            ['label' => 'Создание хоста', 'url' => ['/site/create']],
            [
                'label' => (!\Yii::$app->getUser()->isGuest)? Yii::$app->user->identity->username : "",
                'items' => $available_actions,
            ],
            /*'<div class="dropdown">'
                .'<a href="#" data-toggle="dropdown" class="dropdown-toggle">'.Yii::$app->user->identity->username.' <b class="caret"></b></a>'
                    .Dropdown::widget([
                        'items' => [
                            ['label' => '', 'url' => 'user','class' => 'glyphicon glyphicon-pencil'],
                            ['label' => '', 'url' => 'logout','class' => 'glyphicon glyphicon-off'],
                        ],
                    ])
            .'</div>'*/
        ],
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            'homeLink' => ['label' => 'Главная', 'url' => '/'],
        ]) ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; Digital Spectr <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
