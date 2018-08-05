<?php

namespace Company\Related\Test\Helper;

use PHPUnit\Framework\TestCase;

use Magento\Catalog\Helper\Image;
use Magento\Catalog\Helper\ImageFactory;
use Magento\Framework\View\Element\Template\Context;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

use Company\Related\Model\Related as RelatedModel;
use Company\Related\Model\ResourceModel\Related as ResourceModelRelated;
use Company\Related\Model\ResourceModel\Related\Collection as RelatedCollection;
use Company\Related\Model\ResourceModel\RelatedFactory as ResourceRelatedFactory;
use Company\Related\Block\Related;

/**
 * Class RelatedTest
 * @package Company\Related\Test\Helper
 */
class RelatedTest extends TestCase
{

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Related
     */
    protected $_related;

    /**
     * set up mocks
     */
    protected function setUp()
    {
        $context = $this->createMock(Context::class);

        $objectManager = new ObjectManager($this);


        $productCollection = $this->createMock(Collection::class);
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

        $image = $this->createMock(Image::class);
        $imageFactory = $this->createMock(ImageFactory::class);

        $imageFactory->expects($this->any())
            ->method('create')
            ->willReturn($image);

        $relatedModel = $objectManager->getObject(RelatedModel::class,
            ['resourceRelatedFactory' => $resourceRelatedFactory]
        );

        $this->_related = new Related(
            $context,
            $relatedModel,
            $imageFactory,
            []
        );
    }

    /**
     * unset data
     */
    protected function tearDown()
    {
        unset($this->_related);
    }

    /**
     * test product collection
     */
    public function testGetProductCollection()
    {
        $result = $this->_related->getProductCollection();

        $this->assertInstanceOf(
            Collection::class,
            $result
        );
    }

    /**
     * test image helper
     */
    public function testGetImageHelper()
    {
        $result = $this->_related->getImageHelper();

        $this->assertInstanceOf(
            Image::class,
            $result
        );
    }
}
