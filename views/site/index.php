<?php

use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Modal;

$this->title = 'My Yii Application';
?>
<div class="site-index">
	<p>
		<?= Html::a('Создать рандомную запись', ['create', 'random' => true], ['id' => 'create-random-user-link', 'class' => 'btn btn-primary']) ?>
		<?php
		Modal::begin([
			'id' => 'user-modal',
			'size' => 'modal-lg',
			'toggleButton' => [
				'id' => 'create-user-link',
				'label' => 'Создать новую запись',
				'tag' => 'a',
				'class' => 'btn btn-success',
				'href' => Url::to(['site/create']),
				'data' => [
					'target' => '#user-modal',
				],
			],
		]);
		Modal::end();
		?>
    </p>

	<?php Pjax::begin(['id' => 'users_pjax']) ?>
		<?= GridView::widget([
			'dataProvider' => $dataProvider,
			'layout'=>'{summary}' . $buttons . '{items}{pager}',
			'tableOptions' => [
				'id' => 'TestTable',
				'class' => 'table table-striped table-bordered',
			],
			'columns' => [
				//'id',
				'name',
				[
					'attribute' => 'city_id',
					'content' => function($data){
						return $data->city->name;
					},
				],
				[
					'attribute' => 'skills',
					'content' => function($user) {
						/*
						$skills = array_map(function ($id, $skills) {
							return $id . ':' . $skills['name'];
						}, array_keys($user->skills), $user->skills);
						
						return implode(', ', $skills);
						*/
						$skills = '';
						foreach($user->skills as $skill) {
							$skills .= $skill['name'] . ', ';
						}
						
						return rtrim($skills, ', ');
					},
				],
				[
					'class' => 'yii\grid\ActionColumn',
					'template' => '{delete}',
					'contentOptions' => ['class' => 'action-column'],
					'buttons' => [
						'delete' => function ($url, $model) {
							return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
								'class' => 'delete-link',
								'data-pjax' => '#users_pjax',
								'title' => Yii::t('yii', 'Delete')
							]);
						}
					],
				],
			],
		]); ?>
	<?php Pjax::end() ?>
	
</div>
