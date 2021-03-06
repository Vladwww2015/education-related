<?php

namespace Company\Related\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Catalog\Helper\ImageFactory;

use Company\Related\Model\Related as RelatedModel;

/**
 * Class Related
 * @package Company\Related\Block
 */
class Related extends Template
{
    /**
     * @var RelatedModel
     */
    protected $_related;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected $_collection = null;

    /**
     * @var ImageFactory
     */
    protected $_imageHelperFactory;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $_imageHelper;

    /**
     * Related constructor.
     * @param Context $context
     * @param RelatedModel $related
     * @param ImageFactory $imageHelperFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        RelatedModel $related,
        ImageFactory $imageHelperFactory,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_related = $related;
        $this->_imageHelperFactory = $imageHelperFactory;
    }

    /**
     * @return null|\Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function getProductCollection()
    {
        if($this->_collection === null) {
            $this->_collection = $this->_related->getRelatedProductCollection();
        }

        return $this->_collection;
    }

    /**
     * @return \Magento\Catalog\Helper\Image
     */
    public function getImageHelper()
    {
        if(!$this->_imageHelper) {
            $this->_imageHelper = $this->_imageHelperFactory->create();
        }

        return $this->_imageHelper;
    }
}
