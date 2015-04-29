<?php
/**
 * Created by PhpStorm.
 * User: asu5
 * Date: 28.04.2015
 * Time: 9:04
 */

namespace frontend\helpers;

class InterviewHelper
{
    public $model;
    public $attributes;

    public function insertData()
    {
        if ($this->model->hasErrors() === false) {

            $attributes = $this->attributes;
            $values = $this->model->getAttributes($attributes);

            foreach ($values as &$value) {
                if (is_array($value)) {
                    $value = implode(' ', $value);
                }
            }

            $attributesAsString = implode(', ', $attributes);
            $values = array_map(
                function ($v) {
                    return '"' . $v . '"';
                },
                $values
            );
            $values = implode(', ', $values);

            return \Yii::$app->db->createCommand(
                "INSERT INTO interview ($attributesAsString) VALUES ($values)"
            )->execute();

        } else {
            return false;
        }
    }
}