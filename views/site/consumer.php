<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;

if ($messageCount == 0) {
	$pointer_events = "pointer-events: none;";
	$opacity = "opacity: 0.5;";
}

$pointer_events = "pointer-events: all;";
$opacity = "opacity: 1;";

?>

<div class="add-rabbit-message">
	<h1><?= Html::encode($this->title) ?></h1>
	<div class="add-rabbit-message-form">
		<?php
            if ($error_count == 1) {
                echo "<h5 style='color: red;'>Превышен лимит на количество выгружаемых сообщений... Обратите внимание на счетчик выгрузки доступных сообщений ниже...</h5>";
            }
		?>
		<?php $form = ActiveForm::begin(); ?>
            <?= $form->field($model, 'int_message_count', ['template' => "<strong>{label}</strong><br/><strong>{input}</strong><strong>{hint}</strong><strong style='color: #f00;'>{error}</strong>\n"])->label("Сколько сообщений вы хотите выгрузить из rabbit? Максимальное возможное количество для выгрузки составляет: ".$messageCount." сообщений (-ие, -ия)!")->textInput() ?>
            <div class="form-group">
                <?= Html::submitButton('Получить из rabbit-a', ['class' => "btn btn-success", "style" => $pointer_events . " " . $opacity]) ?>
            </div>
		<?php ActiveForm::end(); ?>
	</div>
    <?php if (!empty($alls_queues_rabbits_shows)) { ?>
        <h5>Вывод данных из очереди:</h5>
        <table border="1px solid black">
            <tr>
                <th>Номер записи</th>
                <th>Текст сообщения</th>
            </tr>
            <?php
                foreach ($alls_queues_rabbits_shows as $all_queue_rabbit_show) {
                    echo "<tr>
                        <td>" . $all_queue_rabbit_show["id"] . "</td>
                        <td>" . nl2br($all_queue_rabbit_show["text_message"]) . "</td>
                    </tr>";
                }
            ?>
        </table>
    <?php } ?>
</div>