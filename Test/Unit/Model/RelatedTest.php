<?php

namespace Company\Related\Test\Unit\Model;

use PHPUnit\Framework\TestCase;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;

use Company\Related\Model\Related;
use Company\Related\Model\ResourceModel\Related as ResourceModelRelated;
use Company\Related\Model\ResourceModel\Related\Collection as RelatedCollection;
use Company\Related\Model\ResourceModel\RelatedFactory as ResourceRelatedFactory;

/**
 * Class RelatedTest
 * @package Company\Related\Test\Unit\Model
 */
class RelatedTest extends TestCase
{
    /**
     * @var Related
     */
    protected $_related;

    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $productCollection = $this->createMock(ProductCollection::class);
        $relatedCollection = $this->createMock(RelatedCollection::class);
        $resourceRelated = $this->createMock(ResourceModelRelated::class);

        $resourceRelated->expects($this->any())
            ->method('getRelatedProductCollection')
            ->willReturn($productCollection);

        $resourceRelatedFactory = $this->createMock(ResourceRelatedFactory::class,
            ['relatedCollection' => $relatedCollection]
        );

        $resourceRelatedFactory->expects($this->any())
            ->method('create')
            ->willReturn($resourceRelated);

        $this->_related = $objectManager->getObject(Related::class,
            ['resourceRelatedFactory' => $resourceRelatedFactory]
        );
    }

    protected function tearDown()
    {
        unset($this->_related);
    }

    public function testGetRelatedProductCollection()
    {
        $result = $this->_related->getRelatedProductCollection();

        $this->assertInstanceOf(
            ProductCollection::class,
            $result
        );
    }
}
