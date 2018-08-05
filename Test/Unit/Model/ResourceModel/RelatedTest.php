<?php

namespace Company\Related\Test\Unit\Model\ResourceModel;

use Magento\Catalog\Model\Product;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Customer;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;

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

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $_customer;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected $_productCollection;

    protected function setUp()
    {
        $this->_resourceRelated = $this->createMock(Related::class);
        $this->_product = $this->createMock(Product::class);

        $this->_store = $this->createMock(StoreInterface::class);
        $this->_storeManager = $this->createMock(StoreManagerInterface::class);

        $this->_customerSession = $this->createMock(Session::class);

        $this->_customer = $this->createMock(Customer::class);
        $this->_customerSession->expects($this->once())->method('getCustomer')->willReturn($this->_customer);

        $this->_resourceRelated->expects($this->any())
            ->method('getProduct')
            ->willReturn($this->_product);

        $this->_productCollection = $this->createMock(ProductCollection::class);


        $this->_resourceRelated->expects($this->any())
            ->method('getCustomer')
            ->willReturn($this->_customerSession->getCustomer());

        $this->_storeManager->expects($this->any())
            ->method('getStore')
            ->willReturn($this->_store);

        $this->_store->expects($this->any())
            ->method('getId')
            ->willReturn(1);
    }

    protected function tearDown()
    {
        unset($this->_resourceRelated);
        unset($this->_product);
        unset($this->_store);
        unset($this->_storeManager);
        unset($this->_customerSession);
        unset($this->_resourceRelated);
        unset($this->_storeManager);
        unset($this->_store);
        unset($this->_productCollection);
    }

    public function testGetCustomer()
    {
        $result = $this->_resourceRelated->getCustomer();
        $this->assertInstanceOf(
            Customer::class,
            $result
        );
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

    public function testGetRelatedProductCollection()
    {
        $result = $this->_productCollection;

        $this->assertInstanceOf(
            ProductCollection::class,
            $result
        );
    }
}
