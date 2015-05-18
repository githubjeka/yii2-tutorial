<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Star;

/**
 * SearchStar represents the model behind the search form about `common\models\Star`.
 */
class SearchStar extends Star
{
    public $countPlanets;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'countPlanets'], 'integer'],
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
        $query = Star::find()
            ->select([$this->tableName() . '.*', 'count(star_id) as countPlanets'])
            ->joinWith('planets')
            ->groupBy($this->tableName() . '.id');;

        $dataProvider = new ActiveDataProvider(
            [
                'query' => $query,
                'sort' => [
                    'attributes' => [
                        'id',
                        'name',
                        'countPlanets' => [
                            'asc' => ['countPlanets' => SORT_ASC,],
                            'desc' => ['countPlanets' => SORT_DESC,],
                        ],
                    ]
                ]
            ]
        );

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        if ($this->countPlanets) {
            $query->having(['countPlanets' => (int)$this->countPlanets]);
        }

        $query->andFilterWhere(
            [
                $this->tableName() . 'id' => $this->id,
            ]
        );

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
