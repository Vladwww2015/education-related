<?php

namespace Company\Related\Model;

use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Visitor;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\App\ResourceConnection;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;

use Company\Related\Model\ResourceModel\Related as ResourceRelated;

use Company\Related\Helper\Data as Helper;

/**
 * Class Related
 * @package Company\Related\Model
 */
class Related extends AbstractModel
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
     * @var HelperConfig
     */
    protected $_helper;


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
     * @param Context $context
     * @param Registry $registry
     * @param Session $customerSession
     * @param StoreManagerInterface $storeManager
     * @param Helper $helper
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Visitor $customerVisitor,
        Session $customerSession,
        StoreManagerInterface $storeManager,
        Helper $helper,
        ResourceConnection $resourceConnection,
        ProductCollectionFactory $productCollectionFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        $this->_helper          = $helper;
        $this->_storeManager    = $storeManager;
        $this->_customerSession = $customerSession;
        $this->_customerVisitor = $customerVisitor;
        $this->_productCollectionFactory = $productCollectionFactory;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_resource = $resourceConnection;
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
     * @return array
     */
    public function getProductRelatedIds()
    {
        if(!count($this->_productIds)) {
            $tableName = $this->_resource->getTableName(self::TABLE_NAME);

            $customerIds = trim(implode(',', $this->getCustomerIds()), ',');
            $visitorIds = trim(implode(',', $this->getVisitorIds()), ',');

            $query = $this->getQuery($customerIds, $visitorIds);

            if(count($this->getCustomerIds()) || count($this->getVisitorIds())) {
                $currentProductIds = $this->_resource->getConnection()
                    ->fetchAll('SELECT ' . self::PRODUCT_ID. ' FROM ' . $tableName . '
                     WHERE ' . self::PRODUCT_ID. ' NOT IN (' . $this->getProduct()->getId() . ')' . $query . '
                        AND ' . self::STORE_ID. ' = ' . $this->getStoreId());

                $this->_productIds = $this->_helper->getIdsArray($currentProductIds, self::PRODUCT_ID);
            }
        }

        return $this->_productIds;
    }

    /**
     * init resource model
     */
    protected function _construct()
    {
        $this->_init(ResourceRelated::class);
    }

    /**
     * @param $customerIds
     * @param $visitorIds
     * @return string
     */
    protected function getQuery($customerIds, $visitorIds)
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
            $tableName = $this->_resource->getTableName(self::TABLE_NAME);
            $customerIds = $this->_resource->getConnection()
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
            $tableName = $this->_resource->getTableName(self::TABLE_NAME);
            $visitorIds = $this->_resource->getConnection()
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