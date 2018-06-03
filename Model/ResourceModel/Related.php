<?php

namespace Company\Related\Model\ResourceModel;

use Magento\Framework\Registry;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Visitor;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;

use Company\Related\Helper\Data as Helper;

/**
 * Class Related
 * @package Company\Related\Model\ResourceModel
 */
class Related extends AbstractDb
{

    const CACHE_TAG   = 'company_related';

    const STORE_ID    = 'store_id';

    const TABLE_NAME  = 'report_viewed_product_index';

    const VISITOR_ID  = 'visitor_id';

    const PRODUCT_ID  = 'product_id';

    const CUSTOMER_ID = 'customer_id';

    /**
     * @var string
     */
    protected $_cacheTag = 'company_related';

    /**
     * @var string
     */
    protected $_eventPrefix = 'company_related';

    /**
     * @var
     */
    protected $_product;

    /**
     * @var Session
     */
    protected $_customerSession;

    /**
     * @var Visitor
     */
    protected $_customerVisitor;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Helper
     */
    protected $_helper;

    /**
     * @var Registry
     */
    protected $_registry;


    /**
     * @var ProductCollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @var array
     */
    protected $_productIds = [];

    /**
     * @var array
     */
    protected $_customerIds = [];

    /**
     * @var array
     */
    protected $_visitorIds = [];

    /**
     * Related constructor.
     * @param Helper $helper
     * @param Context $context
     * @param Registry $registry
     * @param Visitor $customerVisitor
     * @param Session $customerSession
     * @param StoreManagerInterface $storeManager
     * @param ProductCollectionFactory $productCollectionFactory
     * @param AbstractResource|null $resource
     * @param null $connectionName
     */
    public function __construct(
        Helper $helper,
        Context $context,
        Registry $registry,
        Visitor $customerVisitor,
        Session $customerSession,
        StoreManagerInterface $storeManager,
        ProductCollectionFactory $productCollectionFactory,
        AbstractResource $resource = null,
        $connectionName = null
    )
    {
        $this->_helper          = $helper;
        $this->_registry        = $registry;
        $this->_storeManager    = $storeManager;
        $this->_customerSession = $customerSession;
        $this->_customerVisitor = $customerVisitor;
        $this->_productCollectionFactory = $productCollectionFactory;
        parent::__construct($context, $connectionName);
    }

    /**
     * @return \Magento\Customer\Model\Customer
     */
    public function getCustomer()
    {
        return $this->_customerSession->getCustomer();
    }

    /**
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        if(!$this->_product) {
            $this->_product = $this->_registry->registry('current_product');
        }
        return $this->_product;
    }

    /**
     * @return int
     */
    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    /**
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function getRelatedProductCollection()
    {
        $productCollection = $this->_productCollectionFactory->create();

        $productIds = array_unique($this->getProductRelatedIds());

        return  $productCollection->addAttributeToSelect(['thumbnail', 'url_key'])
            ->addFieldToFilter('entity_id', ['in' => [$productIds]])
            ->setPageSize($this->_helper->getRelatedProductQty());
    }

    /**
     * init table and set primary key
     */
    protected function _construct()
    {
        $this->_init('report_viewed_product_index', 'index_id');
    }

    /**
     * @return array
     */
    protected function getProductRelatedIds()
    {
        if(!count($this->_productIds)) {
            $tableName = $this->_resources->getTableName(self::TABLE_NAME);

            $customerIds = trim(implode(',', $this->getCustomerIds()), ',');
            $visitorIds = trim(implode(',', $this->getVisitorIds()), ',');

            $query = $this->_getQuery($customerIds, $visitorIds);

            if(count($this->getCustomerIds()) || count($this->getVisitorIds())) {
                $currentProductIds = $this->_resources->getConnection()
                    ->fetchAll('SELECT ' . self::PRODUCT_ID. ' FROM ' . $tableName . '
                     WHERE ' . self::PRODUCT_ID. ' NOT IN (' . $this->getProduct()->getId() . ')' . $query . '
                        AND ' . self::STORE_ID. ' = ' . $this->getStoreId());

                $this->_productIds = $this->_helper->getIdsArray($currentProductIds, self::PRODUCT_ID);
            }
        }
        $productIds = count($this->_productIds) ? $this->_productIds : [0];

        return $productIds;
    }

    /**
     * @param $customerIds
     * @param $visitorIds
     * @return string
     */
    protected function _getQuery($customerIds, $visitorIds)
    {
        $customerIdQuery = count($this->getCustomerIds()) ?
            self::CUSTOMER_ID. ' IN (' . $customerIds . ')' : '';

        $visitorIdQuery = count($this->getVisitorIds()) ?
            self::VISITOR_ID . ' IN (' . $visitorIds . ')' : '';

        switch (true) {
            case (mb_strlen($customerIdQuery) && mb_strlen($visitorIdQuery)) :
                return 'AND ( ' . $visitorIdQuery . ' OR ' . $customerIdQuery . ' )';
            case mb_strlen($customerIdQuery):
                return 'AND ' . $customerIdQuery;
            case mb_strlen($visitorIdQuery):
                return 'AND ' . $visitorIdQuery;
            default:
                return '';
        }
    }

    /**
     * @return array
     */
    protected function getCustomerIds()
    {
        if(!count($this->_customerIds)) {
            $tableName = $this->_resources->getTableName(self::TABLE_NAME);
            $customerIds = $this->_resources->getConnection()
                ->fetchAll('SELECT ' . self::CUSTOMER_ID . ' FROM ' . $tableName .
                    ' WHERE ' . self::CUSTOMER_ID. ' NOT IN (' . $this->getCustomerId() . ') 
                AND ' . self::PRODUCT_ID. ' = ' . $this->getProduct()->getId() . '
                AND ' . self::STORE_ID. ' = ' . $this->getStoreId() . '
                ');

            $this->_customerIds = $this->_helper->getIdsArray($customerIds, self::CUSTOMER_ID);
        }

        return $this->_customerIds;
    }

    /**
     * @return array
     */
    protected function getVisitorIds()
    {
        if(!count($this->_visitorIds)) {
            $currentVisitorId = $this->_customerVisitor->getId();
            $tableName = $this->_resources->getTableName(self::TABLE_NAME);
            $visitorIds = $this->_resources->getConnection()
                ->fetchAll('SELECT ' . self::VISITOR_ID. ' FROM ' . $tableName .
                    ' WHERE ' . self::VISITOR_ID. ' NOT IN ("' . $currentVisitorId . '")
                AND ' . self::PRODUCT_ID . ' = ' . $this->getProduct()->getId() . '
                AND ' . self::STORE_ID . ' = ' . $this->getStoreId() . '
                ');

            $this->_visitorIds = $this->_helper->getIdsArray($visitorIds, self::VISITOR_ID);
        }
        return $this->_visitorIds;
    }


    /**
     * @return int|mixed
     */
    protected function getCustomerId()
    {
        if($this->getCustomer()) {
            $customerId = $this->getCustomer()->getId();
            if($customerId) {
                return $this->getCustomer()->getId();
            }
        }
        return 0;
    }
}
