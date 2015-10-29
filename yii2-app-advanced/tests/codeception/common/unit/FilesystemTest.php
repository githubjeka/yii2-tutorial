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
        $this->specify(
            'Interview file should be correct according to description step',
            function () use ($I) {
                $I->amInPath(Yii::getAlias('@common/models'));
                $I->openFile('Interview.php');
                $I->seeInThisFile("namespace frontend\\models; // измените на \"namespace common\\models;\"");

            }
        );
    }
}
