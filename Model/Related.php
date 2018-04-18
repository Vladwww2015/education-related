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
    const CACHE_TAG = 'company_related';

    const TABLE_NAME = 'report_viewed_product_index';

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
        $productIds = $this->getProductRelatedIds();
        return  $productCollection->addAttributeToSelect(['thumbnail', 'url_key'])
            ->addFieldToFilter('entity_id', ['in' => [$productIds]])
            ->setPageSize($this->_helper->getRelatedProductQty())->load();
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
     * TODO refactoring
     */
    public function getProductRelatedIds()
    {
        if(!count($this->_productIds)) {
            $tableName = $this->_resource->getTableName(self::TABLE_NAME);

            $customerIds = trim(implode(',', $this->getCustomerIds()), ',');
            $visitorIds = trim(implode(',', $this->getVisitorIds()), ',');

            $customerIdQuery = count($this->getCustomerIds()) ?
                'customer_id IN (' . $customerIds . ')' : '';

            $visitorIdQuery = count($this->getVisitorIds()) ?
                'visitor_id IN (' . $visitorIds . ')' : '';

            $query = '';
            if (mb_strlen($customerIdQuery) && mb_strlen($visitorIdQuery)) {
                $query = 'AND ( ' . $visitorIdQuery . ' OR ' . $customerIdQuery . ' )';
            } elseif (mb_strlen($customerIdQuery)) {
                $query = 'AND ' . $customerIdQuery;
            } elseif (mb_strlen($visitorIdQuery)) {
                $query = 'AND ' . $visitorIdQuery;
            }
            if(mb_strlen($customerIdQuery) || mb_strlen($visitorIdQuery)) {
                $currentProductIds = $this->_resource->getConnection()
                    ->fetchAll('SELECT product_id FROM ' . $tableName . '
                     WHERE product_id NOT IN (' . $this->getProduct()->getId() . ')' . $query . '
                        AND store_id = ' . $this->getStoreId());

                $this->_productIds = $this->_helper->getIdsArray($currentProductIds, 'product_id');
            }
        }
        return $this->_productIds;
    }

    protected function _construct()
    {
        $this->_init(ResourceRelated::class);
    }
    
    /**
     * @return array
     */
    protected function getCustomerIds()
    {
        if(!count($this->_customerIds)) {
            $tableName = $this->_resource->getTableName(self::TABLE_NAME);
            $customerIds = $this->_resource->getConnection()
                ->fetchAll('SELECT customer_id FROM ' . $tableName .
                    ' WHERE customer_id NOT IN (' . $this->getCustomerId() . ') 
                AND product_id = ' . $this->getProduct()->getId() . '
                AND store_id = ' . $this->getStoreId() . '
                ');

            $this->_customerIds = $this->_helper->getIdsArray($customerIds, 'customer_id');
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
                ->fetchAll('SELECT visitor_id FROM ' . $tableName .
                    ' WHERE visitor_id NOT IN ("' . $currentVisitorId . '")
                AND product_id = ' . $this->getProduct()->getId() . '
                AND store_id = ' . $this->getStoreId() . '
                ');

            $this->_visitorIds = $this->_helper->getIdsArray($visitorIds, 'visitor_id');
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