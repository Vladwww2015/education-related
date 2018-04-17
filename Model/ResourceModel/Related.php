<?php

namespace Company\Related\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;


class Related extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('report_viewed_product_index', 'index_id');
    }
}