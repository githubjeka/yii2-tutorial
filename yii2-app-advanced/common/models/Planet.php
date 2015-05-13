<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "planet".
 *
 * @property integer $id
 * @property string $name
 * @property integer $star_id
 *
 * @property Star $star
 * @property Satellite[] $satellites
 */
class Planet extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'planet';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'star_id'], 'required'],
            [['star_id'], 'integer'],
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
            'name' => 'Name',
            'star_id' => 'Star ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStar()
    {
        return $this->hasOne(Star::className(), ['id' => 'star_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSatellites()
    {
        return $this->hasMany(Satellite::className(), ['planet_id' => 'id']);
    }
}
