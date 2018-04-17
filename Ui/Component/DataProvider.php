<?php

namespace Company\Related\Ui\Component;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider as UiDataProvider;

use Company\Related\Model\ResourceModel\Related\Collection as RelatedCollection;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;

/**
 * Class DataProvider
 * @package Company\Related\Ui\Component
 */
class DataProvider extends UiDataProvider
{
    const QTY          = 'qty';

    const NAME         = 'name';

    const ITEMS        = 'items';

    const ENTITY_ID    = 'entity_id';

    const PRODUCT_NAME = 'product_name';

    const TOTAL_RECORDS = 'totalRecords';

    /**
     * @var RelatedCollection
     */
    protected $_relatedCollection;

    /**
     * @var ProductCollection
     */
    protected $_productCollection;

    /**
     * @var array
     */
    protected $_result = [];

    /**
     * DataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param ReportingInterface $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param RelatedCollection $relatedCollection
     * @param ProductCollection $productCollection
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        RelatedCollection $relatedCollection,
        ProductCollection $productCollection,
        array $meta = [],
        array $data = []
    )
    {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $reporting, $searchCriteriaBuilder, $request, $filterBuilder, $meta, $data);
        $this->_relatedCollection = $relatedCollection;
        $this->_productCollection = $productCollection;
    }

    /**
     * @return array
     */
    public function getData()
    {
        if(!count($this->_result)) {
            $visitorsQty = $this->getProductVisitorsQty();

            $this->_productCollection
                ->addFieldToFilter(self::ENTITY_ID, ['in' => $this->getProductIds()])
                ->addAttributeToSelect(self::NAME);

            foreach ($this->_productCollection as $product) {
                if(isset($visitorsQty[$product->getId()])) {
                    $this->_result[self::ITEMS][] = [
                        static::ENTITY_ID    => $product->getId(),
                        static::PRODUCT_NAME => $product->getName(),
                        static::QTY          => $visitorsQty[$product->getId()]
                    ];
                }
            }

            $this->_result[self::TOTAL_RECORDS] = $this->_productCollection->getSize();
        }
        return $this->_result;
    }

    /**
     * @return array
     */
    protected function getProductIds()
    {
        return array_unique($this->_relatedCollection->getProductIds());
    }

    /**
     * @return array
     */
    protected function getProductVisitorsQty()
    {
        return $this->_relatedCollection->getProductVisitorQty();
    }
}