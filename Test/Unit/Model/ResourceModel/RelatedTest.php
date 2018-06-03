<?php

namespace Company\Related\Test\Unit\Model\ResourceModel;

use Magento\Catalog\Model\Product;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;

use PHPUnit\Framework\TestCase;

use Company\Related\Model\ResourceModel\Related;

/**
 * Class RelatedTest
 * @package Company\Related\Test\Unit\Model\ResourceModel
 */
class RelatedTest extends TestCase
{
    /**
     * @var \Company\Related\Model\ResourceModel\Related
     */
    protected $_resourceRelated;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_product;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface;
     */
    protected $_storeManager;

    /**
     * @var \Magento\Store\Api\Data\StoreInterface;
     */
    protected $_store;

    protected function setUp()
    {
        $this->_resourceRelated = $this->createMock(Related::class);
        $this->_product = $this->createMock(Product::class);

        $this->_store = $this->createMock(StoreInterface::class);
        $this->_storeManager = $this->createMock(StoreManagerInterface::class);

        $this->_resourceRelated->expects($this->any())
            ->method('getProduct')
            ->willReturn($this->_product);

        $this->_resourceRelated->expects($this->any())
            ->method('_getQuery')
            ->willReturn($this->_getQueryString());

        $this->_storeManager->expects($this->any())
            ->method('getStore')
            ->willReturn($this->_store);

        $this->_store->expects($this->any())
            ->method('getId')
            ->willReturn(1);
    }

    protected function tearDown()
    {
    }

    public function getCustomerTest()
    {
        // \Magento\Customer\Model\Customer
    }

    public function testGetProduct()
    {
        $result = $this->_resourceRelated->getProduct();

        $this->assertInstanceOf(
            Product::class,
            $result
        );
    }

    public function testGetStoreId()
    {
        $result = $this->_storeManager->getStore()->getId();

        $this->assertEquals(1, $result);
    }

    /**
     * @dataProvider productRelatedIds
     *
     * @param string  $expression
     * @param bool    $expected
     */
    public function testGetProductRelatedIds(

    )
    {

    }


    public function getCustomerIdsTest()
    {

    }

    public function getVisitorIdsTest()
    {

    }

    public function getCustomerIdTest()
    {

    }

    public function getDataProviderCustomerVisitorIds()
    {
        return [];
    }

    protected function _getQueryString()
    {
        
    }
}
