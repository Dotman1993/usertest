<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="users-record-form">

    <?php $form = ActiveForm::begin(); ?>
	
    <?= $form->field($user, 'name')->textInput(['maxlength' => true]) ?>
	
	<?= $form->field($city, 'name')->textInput(['maxlength' => true]) ?>

	<fieldset>
	<legend>Навыки</legend>
	
	<button id="button-add-skill" type="button" class="btn btn-default">
		<i class="glyphicon glyphicon-plus"></i>
	</button>
	
	<div class="skills clearfix">
		<?php
		foreach ($skills as $index => $skill) {
			if(!empty($skill->name)) {
				echo '<div class="form-group form-group-sm col-sm-6 col-md-4">';
				echo $form->field($skill, "[$index]name")->textInput(['maxlength' => true, 'required' => false]);
				echo '</div>';
			}
		}
		?>
		
		<div id="fields-add-skill" class="form-group form-group-sm col-sm-6 col-md-4 hidden">
			<?= Html::a('<i class="glyphicon glyphicon-remove"></i>', '#', ['class' => 'remove-skill', 'style' => ['float' => 'right']]);?>
			<?= $form->field($skill, "[$index]name")->textInput(['maxlength' => true, 'id' => false, 'name' => false, 'value' => false, 'data' => ['attribute' => 'name', 'input_name' => $skill->formName()]]) ?>
		</div>
	</div>
	<fieldset>

    <div class="form-group">
        <?= Html::submitButton($user->isNewRecord ? 'Создать' : 'Изменить', ['class' => $user->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
$js = <<<JS
$('#fields-add-skill.hidden').find('.form-group').each(function() {
	$(this).removeAttr('class').addClass('form-group');
	//$(this).removeClass('has-error').addClass('form-group');
});
var i = $index;
$('#button-add-skill').on('click', function () {
	var skill = $('#fields-add-skill.hidden').clone().removeAttr('id').removeClass('hidden');
	
	var inputs = skill.find('select, input');
	var input_name = inputs.eq(0).data('input_name') + '[' + i++ + ']';

	inputs.each(function(index) {
		var el = inputs.eq(index);
		el.attr('name', input_name + '[' + el.data('attribute') + ']');
	});

	skill.appendTo('.skills');
	
	return false;
});

$('form').on('click', '.remove-skill', function(e){
	$(this).parent().remove();
	e.preventDefault();
});
JS;

$this->registerJs($js);
?>