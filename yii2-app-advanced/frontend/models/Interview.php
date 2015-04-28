<?php

namespace frontend\models;

use yii\base\Model;

/**
 * Class Interview
 * Модель, которая описывает форму "Опрос"
 *
 *
 */
class Interview extends Model
{
    public $name;
    public $sex;
    public $planets;
    public $astronauts;
    public $planet;
    public $verifyCode;

    public function rules()
    {
        return [
            [['name', 'sex', 'planets', 'astronauts', 'planet', 'verifyCode'], 'required'],
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
            ['verifyCode', 'captcha'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Имя',
            'sex' => 'Пол',
            'planets' => 'Какие планеты обитаемы?',
            'astronauts' => 'Какие космонавты известны?',
            'planet' => 'На какую планету хотели бы полететь?',
            'verifyCode' => 'Проверочный код',
        ];
    }
}