<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 9/12/14
 * Time: 5:39 PM
 */

namespace Giftcards\FixedWidth\Tests\Spec;


use Giftcards\FixedWidth\Spec\FileSpec;
use Giftcards\FixedWidth\Spec\RecordSpec;
use Giftcards\FixedWidth\Tests\TestCase;

class FileSpecTest extends TestCase
{
    public function testGetters()
    {
        $name = $this->getFaker()->word;
        $recordSpec1 = \Mockery::mock('Giftcards\FixedWidth\Spec\RecordSpec');
        $recordSpec2 = \Mockery::mock('Giftcards\FixedWidth\Spec\RecordSpec');
        $recordSpecs = array(
            'record1' => $recordSpec1,
            'record2' => $recordSpec2,
        );
        $width = $this->getFaker()->numberBetween(10, 20);
        $spec = new FileSpec($name, $recordSpecs, $width);
        $this->assertEquals($name, $spec->getName());
        $this->assertSame($recordSpecs, $spec->getRecordSpecs());
        $this->assertSame($recordSpec1, $spec->getRecordSpec('record1'));
        $this->assertSame($recordSpec2, $spec->getRecordSpec('record2'));
    }

    /**
     * @expectedException \Giftcards\FixedWidth\Spec\SpecNotFoundException
     */
    public function testGetFieldSpecWhereNotThere()
    {
        $spec = new FileSpec($this->getFaker()->word, array(), $this->getFaker()->numberBetween(10, 20));
        $spec->getRecordSpec('record1');
    }

    public function testWidthIsAlwaysInt()
    {
        $spec1 = new FileSpec('name', array(), 10);
        $spec2 = new FileSpec('name', array(), '10');
        $this->assertSame($spec1->getWidth(), $spec2->getWidth());
    }
}
 