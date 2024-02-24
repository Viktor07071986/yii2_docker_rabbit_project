<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "add_message_rabbit_mq".
 *
 * @property int $id
 * @property string|null $text_message
 */

class AddMessageRabbitMq extends ActiveRecord {

	public $int_message_count;

	public static function tableName() {
		return 'add_message_rabbit_mq';
	}

	public function rules() {
		return [
			['text_message', 'trim'],
			['text_message', 'string'],
			['text_message', 'required'],
			['int_message_count', 'trim'],
			['int_message_count', 'integer'],
			['int_message_count', 'required']
		];
	}

	public function attributeLabels() {
		return [
			'id' => 'ID',
			'text_message' => 'Текст сообщения в rabbit!',
			'int_message_count' => 'Сколько сообщений вы хотите выгрузить из rabbit?'
		];
	}

}