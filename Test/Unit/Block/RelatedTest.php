<?php

namespace Company\Related\Test\Helper;

use PHPUnit\Framework\TestCase;

use Magento\Framework\View\Element\Template\Context;
use Magento\Catalog\Helper\Image;
use Magento\Catalog\Helper\ImageFactory;

use Company\Related\Model\Related as RelatedModel;
use Company\Related\Model\ResourceModel\RelatedFactory as ResourceRelatedFactory;

use Company\Related\Block\Related;

class RelatedTest extends TestCase
{

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Related
     */
    protected $_related;


    protected function setUp()
    {
        $context = $this->createMock(Context::class);

        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);


        $productCollection = $this->createMock(\Magento\Catalog\Model\ResourceModel\Product\Collection::class);
        $relatedCollection = $this->createMock(\Company\Related\Model\ResourceModel\Related\Collection::class);
        $resourceRelated = $this->createMock(\Company\Related\Model\ResourceModel\Related::class);

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

    public function testGetProductCollection()
    {
        $result = $this->_related->getProductCollection();

        $this->assertInstanceOf(
            \Magento\Catalog\Model\ResourceModel\Product\Collection::class,
            $result
        );
    }

    public function testGetImageHelper()
    {
        $result = $this->_related->getImageHelper();

        $this->assertInstanceOf(
            \Magento\Catalog\Helper\Image::class,
            $result
        );
    }

}
