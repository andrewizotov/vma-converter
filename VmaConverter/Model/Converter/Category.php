<?php

namespace Vma\VmaConverter\Model\Converter;

use Vma\VmaConverter\Helper\Entity\CategoryHelper;
use Vma\VmaConverter\Helper\Data;
use Magento\Framework\ObjectManagerInterface;
use Vma\VmaConverter\API\Client;
/**
 * Class Category
 * @package Vma\VmaConverter\Model\Converter
 */
class Category  extends EntityConverter
{
    /**
     * @var CategoryHelper
     */
    protected $categoryHelper;
    protected $helper;
    protected $config;
    protected $objectMaganer;
    protected $apiClient;
    const LAST_ENTITY_FIELD = 'category_last';
    const PRIMARY_ID = 'collection_id';

    const RANGE_STEP = 50;

    const MAIN_CATEGORY_TABLE = 'catalog_category_entity';

    public function __construct(
        ObjectManagerInterface $objectManager,
        CategoryHelper $categoryHelper,
        Data $helper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Config $config,
        Client $apiClient
    )
    {
        $this->apiClient = $apiClient;
        $this->objectMaganer = $objectManager;
        $this->categoryHelper = $categoryHelper;
        $this->helper = $helper;
        $this->config = $config;

        /* @var $resource \Magento\Framework\App\ResourceConnection */
        $resource = $this->objectMaganer->get('\Magento\Framework\App\ResourceConnection');

        parent::__construct($resource, $storeManager);
    }


    public static function getLastEntityField()
    {
        return self::LAST_ENTITY_FIELD;
    }

    public static function getEntityPrimaryField()
    {
        return self::PRIMARY_ID;
    }


    public function executeAll()
    {
        $selects = $this->prepareSelectsByRange(
            $this->getAllCategories(),
            'entity_id',
            self::RANGE_STEP
        );

        $lastProcessedId = $this->getLastProcessedEntityId();

        foreach ($selects as $select) {
            $res = $this->connection->query($select->__toString());

            $categoryIds = $res->fetchAll(\Zend_Db::FETCH_COLUMN, 0);

            $lastInPack = end($categoryIds);

            if($lastInPack < $lastProcessedId) {
                continue;
            }

            if(count($categoryIds) > 0) {
                $collection = $this->categoryHelper->getCategoryCollectionQuery($categoryIds);
                $records = $this->helper->getCategoriesRecords($collection);
                $this->saveLastEntityId(end($records));
                $this->apiClient->send($records);
            }
        }
    }

    protected function getAllCategories()
    {
        $select = $this->connection->select()->from(
            self::MAIN_CATEGORY_TABLE, ['entity_id']
        );

        return $this->categoriesSelects[] = $select;
    }
}
