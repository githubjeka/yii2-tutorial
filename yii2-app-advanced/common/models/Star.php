<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "star".
 *
 * @property integer $id
 * @property string $name
 *
 * @property Planet[] $planets
 */
class Star extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'star';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Наименование',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlanets()
    {
        return $this->hasMany(Planet::className(), ['star_id' => 'id']);
    }
}
