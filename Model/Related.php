<?php

namespace Company\Related\Model;

use Magento\Framework\Registry;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\ResourceModel\AbstractResource;

use Company\Related\Model\ResourceModel\Related as ResourceRelated;
use Company\Related\Model\ResourceModel\RelatedFactory as ResourceRelatedFactory;


/**
 * Class Related
 * @package Company\Related\Model
 */
class Related extends AbstractModel
{
    /**
     * @var ResourceRelatedFactory
     */
    protected $_resourceRelatedFactory;

    /**
     * Related constructor.
     * @param Context $context
     * @param Registry $registry
     * @param ResourceRelatedFactory $resourceRelatedFactory
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ResourceRelatedFactory $resourceRelatedFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_resourceRelatedFactory = $resourceRelatedFactory;
    }

    /**
     * init resource model
     */
    protected function _construct()
    {
        $this->_init(ResourceRelated::class);
    }

    /**
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function getRelatedProductCollection()
    {
        $resourceRelacted = $this->_resourceRelatedFactory->create();

        return $resourceRelacted->getRelatedProductCollection();
    }
}
