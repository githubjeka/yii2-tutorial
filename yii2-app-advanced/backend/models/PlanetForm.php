<?php

namespace backend\models;

use common\models\Planet;
use common\models\Star;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for work with "Planet" and her relations.
 */
class PlanetForm extends Planet
{
    public $satellitesArray;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
                ['star_id', 'exist', 'targetClass' => Star::className(), 'targetAttribute' => 'id'],
                ['satellitesArray', 'satelliteValidator', 'message' => 'Один из спутников не корректный']
            ]
        );
    }

    public function satelliteValidator($attr, $param)
    {
        $this->addError($attr, isset($param['message']) ? $param['message'] : 'Ошибка валидации спутников');
        return false;
    }
}
