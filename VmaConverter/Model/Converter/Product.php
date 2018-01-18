<?php

namespace Vma\VmaConverter\Model\Converter;

use Vma\VmaConverter\Helper\Entity\ProductHelper;
use Vma\VmaConverter\Helper\Data;
use Magento\Framework\ObjectManagerInterface;
use Vma\VmaConverter\API\Client;

class Product extends EntityConverter
{
    const RANGE_STEP = 100;
    const MAIN_PRODUCT_TABLE = 'catalog_product_entity';

    private $productHelper;
    private $helper;
    protected $apiClient;

    /**
     * Cached all product select by store id
     *
     * @var \Magento\Framework\DB\Select[]
     */
    protected $productsSelects = [];


    /**
     * @var \Magento\Catalog\Model\Config
     */
    protected $config;

    /**
     * Product constructor.
     * @param ObjectManagerInterface $objectManager
     * @param ProductHelper $productHelper
     * @param Data $helper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Config $config
     */
    function __construct(
        ObjectManagerInterface $objectManager,
        ProductHelper $productHelper,
        Data $helper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Config $config,
        Client $apiClient
    )
    {
        $this->apiClient = $apiClient;
        $this->objectMaganer = $objectManager;
        $this->productHelper = $productHelper;
        $this->helper = $helper;
        $this->config = $config;

        /* @var $resource \Magento\Framework\App\ResourceConnection */
        $resource = $this->objectMaganer->get('\Magento\Framework\App\ResourceConnection');

        parent::__construct($resource, $storeManager);
    }

    public function executeAll()
    {
        $selects = $this->prepareSelectsByRange(
            $this->getAllProducts(),
            'entity_id',
            self::RANGE_STEP
        );

        foreach ($selects as $select) {
            $res = $this->connection->query($select->__toString());
            $productIds = $res->fetchAll(\Zend_Db::FETCH_COLUMN, 0);
            if(count($productIds) > 0) {
                $collection = $this->productHelper->getProductCollectionQuery($productIds);
                $records = $this->helper->getProductsRecords($collection);
                $this->apiClient->send($records);
            }
        }
    }

    protected function getAllProducts()
    {

        $select = $this->connection->select()->from(
            self::MAIN_PRODUCT_TABLE, ['entity_id']
        );

        return $this->productsSelects[] = $select;

    }

}