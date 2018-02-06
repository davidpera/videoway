<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * AlquileresSearch represents the model behind the search form of `app\models\Alquileres`.
 */
class AlquileresSearch extends Alquileres
{
    // public $desdeAlquilado;
    //
    // public $hastaAlquilado;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pelicula.codigo', 'socio.numero'], 'integer'],
            [[
                'created_at',
                'devolucion',
                'pelicula.titulo',
                'socio.nombre',
            ], 'safe'],
        ];
    }

    public function attributes()
    {
        return array_merge(parent::attributes(), [
            'pelicula.titulo',
            'pelicula.codigo',
            'socio.numero',
            'socio.nombre',
        ]);
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
     * Creates data provider instance with search query applied.
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Alquileres::find()->joinWith(['pelicula', 'socio']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        $dataProvider->sort->defaultOrder = ['created_at' => SORT_DESC];

        $dataProvider->sort->attributes['pelicula.titulo'] = [
            'asc' => ['peliculas.titulo' => SORT_ASC],
            'desc' => ['peliculas.titulo' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['pelicula.codigo'] = [
            'asc' => ['peliculas.codigo' => SORT_ASC],
            'desc' => ['peliculas.codigo' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['socio.numero'] = [
            'asc' => ['socios.numero' => SORT_ASC],
            'desc' => ['socios.numero' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['socio.nombre'] = [
            'asc' => ['socios.nombre' => SORT_ASC],
            'desc' => ['socios.nombre' => SORT_DESC],
        ];

        // grid filtering conditions
        $query->andFilterWhere([
            'socios.numero' => $this->getAttribute('socio.numero'),
            'peliculas.codigo' => $this->getAttribute('pelicula.codigo'),
            'created_at' => $this->created_at,
            'devolucion' => $this->devolucion,
        ]);

        $query->andFilterWhere([
            'ilike',
            'peliculas.titulo',
            $this->getAttribute('pelicula.titulo'),
        ]);

        $query->andFilterWhere([
            'ilike',
            'socios.nombre',
            $this->getAttribute('socio.nombre'),
        ]);

        return $dataProvider;
    }
}
