<?php

namespace Company\Related\Test\Unit\Controller\Adminhtml\Visitors;

use PHPUnit\Framework\TestCase;

class IndexTest extends TestCase
{
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
}
