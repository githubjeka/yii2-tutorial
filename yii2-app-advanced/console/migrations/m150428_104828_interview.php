<?php

use yii\db\Schema;
use yii\db\Migration;

class m150428_104828_interview extends Migration
{
    public function up()
    {
        $tableOptions = null;

        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%interview}}', [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING . ' NOT NULL',
            'sex' => Schema::TYPE_BOOLEAN . ' NOT NULL',
            'planets' => Schema::TYPE_STRING . ' NOT NULL',
            'astronauts' => Schema::TYPE_STRING. ' NOT NULL',
            'planet' => Schema::TYPE_INTEGER . ' NOT NULL',
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%interview}}');
    }
}
