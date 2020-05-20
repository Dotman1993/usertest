<?php

use yii\helpers\Html;

$this->title = 'Создание новой записи';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="users-record-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'user' => $user,
		'city' => $city,
		'skills' => $skills,
    ]) ?>

</div>
