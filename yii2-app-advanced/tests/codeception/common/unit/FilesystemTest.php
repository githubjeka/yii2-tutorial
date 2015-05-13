<?php

namespace tests\codeception\common\unit\models;

use Codeception\Module\Filesystem;
use Yii;
use tests\codeception\common\unit\TestCase;
use Codeception\Specify;

/**
 * FilesystemTest form test
 */
class FilesystemTest extends TestCase
{
    use Specify;

    public function testFileConfig()
    {
        /** @var Filesystem $I */
        $I = $this->getModule('Filesystem');

        $this->specify('config file should be correct according to description step', function () use ($I) {
            $I->amInPath(Yii::getAlias('@common/config'));
            $I->openFile('main.php');
            $I->seeInThisFile("'name' => 'My company',");
            $I->seeInThisFile("'language' => 'en-En',");
        });

        $this->specify('site controller file must contain ViewAction', function () use ($I) {
            $I->amInPath(Yii::getAlias('@frontend/controllers'));
            $I->openFile('SiteController.php');
            $I->seeInThisFile("'class' => 'yii\web\ViewAction',");
        });
    }
}
