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
     * Используется для проверки каптчи
     * @var string
     */
    public $verifyCode;

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


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'sex', 'planets', 'astronauts', 'planet'], 'required'],
            ['name', 'string'],
            ['sex', 'boolean', 'message' => 'Пол выбран не верно.'],
            [
                ['planets', 'planet'],
                'in',
                'range' => range(0, 7),
                'message' => 'Выбран не корректный список планет.',
                'allowArray' => 1
            ],
            [
                'astronauts',
                'in',
                'range' => range(0, 5),
                'message' => 'Выбран не корректный список космонавтов.',
                'allowArray' => 1
            ],
            ['verifyCode', 'captcha', 'message' => 'Проверочный код введён неверно.',],
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->planets = implode(',', $this->planets);
            $this->astronauts = implode(',', $this->astronauts);
            return true;
        }

        return false;
    }
}
