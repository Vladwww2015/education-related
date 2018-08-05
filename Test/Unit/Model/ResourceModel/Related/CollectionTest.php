<?php

namespace Company\Related\Test\Unit\Model\ResourceModel\Related;

use Company\Related\Model\ResourceModel\Related\Collection as RelatedCollection;

use PHPUnit\Framework\TestCase;

/**
 * Class CollectionTest
 * @package Company\Related\Test\Unit\Model\ResourceModel\Related
 */
class CollectionTest extends TestCase
{
    /**
     * @var \Company\Related\Model\ResourceModel\Related\Collection
     */
    protected $_relatedCollection;

    protected function setUp()
    {
        $this->_relatedCollection = $this->createMock(RelatedCollection::class);
        $this->_relatedCollection->expects($this->any())
            ->method('getProductIds')
            ->willReturn($this->_getProductIds());

        $this->_relatedCollection->expects($this->any())
            ->method('getProductVisitorQty')
            ->willReturn(count($this->_getProductIds()));
    }

    protected function tearDown()
    {
        unset($this->_relatedCollection);
    }

    public function testGetProductIds()
    {
        $this->assertEquals($this->_getProductIds(), $this->_relatedCollection->getProductIds());
    }

    public function testGetProductVisitorQty()
    {
        $this->assertEquals(4, $this->_relatedCollection->getProductVisitorQty());
    }

    /**
     * @return array
     */
    protected function _getProductIds()
    {
        return [
            52,
            34,
            78,
            64
        ];
    }
}
