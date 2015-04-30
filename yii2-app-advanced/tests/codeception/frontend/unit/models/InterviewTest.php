<?php

namespace tests\codeception\frontend\unit\models;

use tests\codeception\frontend\unit\DbTestCase;
use Codeception\Specify;

class InterviewTest extends DbTestCase
{
    use Specify;

    public function testSaveInterview()
    {
        $model = new \frontend\models\Interview(
            [
                'name' => 'Ivanov',
                'sex' => 1,
                'planets' => [1, 2, 3],
                'astronauts' => [2, 3],
                'planet' => 5,
                'verifyCode' => 'testme',
            ]
        );

        $model->save(['name', 'sex', 'planets', 'astronauts', 'planet']);

        $modelFromDb = \Yii::$app->db->createCommand('SELECT * FROM interview WHERE name="Ivanov"')->queryOne();

        $this->specify(
            'Ответы должны быть отправлены',
            function () use ($modelFromDb) {
                expect('имя должно быть сохранено верно', $modelFromDb['name'])->equals('Ivanov');
                expect('пол должен быть сохранен верно', $modelFromDb['sex'])->equals('1');
                expect('планеты должен быть сохранены верно', $modelFromDb['planets'])->equals('1,2,3');
                expect('космонавты должен быть сохранены верно', $modelFromDb['astronauts'])->equals('2,3');
                expect('планета должен быть сохранена верно', $modelFromDb['planet'])->equals(5);
            }
        );
    }

    public function fixtures()
    {
        return [
            'interview' => [
                'class' => 'tests\codeception\frontend\unit\fixtures\InterviewFixture',
            ],
        ];
    }
}
