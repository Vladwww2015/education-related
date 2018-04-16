<?php

namespace Company\Related\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Config
 * @package Company\Related\Helper
 */
class Config extends AbstractHelper
{
    const RELATED_PRODUCT_QTY = 'catalog/customer_product_related/related_qty_product';

    /**
     * @return mixed
     */
     public function getRelatedProductQty()
     {
         return $this->scopeConfig->getValue(
             self::RELATED_PRODUCT_QTY,
             ScopeInterface::SCOPE_STORE
         );
     }

}
