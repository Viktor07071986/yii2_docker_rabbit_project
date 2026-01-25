<?php

use yii\db\Migration;

class m230707_092451_add_message_rabbit_mq extends Migration {

    public function safeUp() {

    }

    public function safeDown() {
        echo "m230707_092451_add_message_rabbit_mq cannot be reverted.\n";
        return false;
    }

    public function up() {
	    $this->createTable('add_message_rabbit_mq', [
		    'id' => $this->primaryKey(),
		    'text_message' => $this->text()->null()
	    ]);
    }

    public function down() {
	    $this->dropTable('add_message_rabbit_mq');
    }

}