<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;

?>

<div class="add-rabbit-message">
	<h1><?= Html::encode($this->title) ?></h1>
	<div class="add-rabbit-message-form">
		<?php $form = ActiveForm::begin(); ?>
            <?= $form->field($model, 'text_message', ['template' => "<strong>{label}</strong><br/><strong>{input}</strong><strong>{hint}</strong><strong style='color: #f00;'>{error}</strong>\n"])->textarea(['rows' => 10]) ?>
            <div class="form-group">
                <?= Html::submitButton('Отправить в rabbit', ['class' => 'btn btn-success']) ?>
            </div>
		<?php ActiveForm::end(); ?>
	</div>
</div>