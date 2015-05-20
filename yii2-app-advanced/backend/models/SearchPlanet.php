<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Planet;

/**
 * SearchPlanet represents the model behind the search form about `common\models\Planet`.
 */
class SearchPlanet extends Planet
{
    public $countSatellites;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'star_id', 'countSatellites'], 'integer'],
            [['name'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Planet::find()
            ->select([$this->tableName() . '.*', 'count(planet_id) as countSatellites'])
            ->joinWith('satellites')
            ->groupBy($this->tableName() . '.id');

        $dataProvider = new ActiveDataProvider(
            [
                'query' => $query,
                'sort' => [
                    'attributes' => [
                        'id',
                        'name',
                        'star_id',
                        'countSatellites' => [
                            'asc' => ['countSatellites' => SORT_ASC,],
                            'desc' => ['countSatellites' => SORT_DESC,],
                        ],
                    ]
                ]
            ]
        );

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        if ($this->countSatellites) {
            $query->having(['countSatellites' => (int)$this->countSatellites]);
        }

        $query->andFilterWhere(
            [
                $this->tableName() . '.id' => $this->id,
                'star_id' => $this->star_id,
            ]
        );

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
