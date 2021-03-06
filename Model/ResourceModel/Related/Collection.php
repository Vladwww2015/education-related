<?php

namespace Company\Related\Model\ResourceModel\Related;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

use Company\Related\Model\Related as ModelRelated;
use Company\Related\Model\ResourceModel\Related as ResourceRelated;

/**
 * Class Collection
 * @package Company\Related\Model\ResourceModel\Related
 */
class Collection extends AbstractCollection
{
    /**
     * @var array
     */
    protected $_productIds = [];

    /**
     * @return array
     */
    public function getProductIds()
    {
        if(!count($this->_productIds)) {
            foreach ($this->getItems() as $item) {
                $this->_productIds[] = $item->getProductId();
            }
        }

        return $this->_productIds;
    }

    /**
     * @return array
     */
    public function getProductVisitorQty()
    {
        return array_count_values($this->getProductIds());
    }

    /**
     * init model and resource model
     */
    protected function _construct()
    {
        $this->_init(ModelRelated::class, ResourceRelated::class);
    }
}
