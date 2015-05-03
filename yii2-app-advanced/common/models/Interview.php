<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "interview".
 *
 * @property integer $id
 * @property string $name
 * @property boolean $sex
 * @property string $planets
 * @property string $astronauts
 * @property integer $planet
 */
class Interview extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'interview';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Имя',
            'sex' => 'Пол',
            'planets' => 'Какие планеты обитаемы?',
            'astronauts' => 'Какие космонавты известны?',
            'planet' => 'На какую планету хотели бы полететь?',
        ];
    }
}
