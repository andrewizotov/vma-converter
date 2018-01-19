<?php

namespace Vma\VmaConverter\Model\Converter;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Query\Generator as QueryGenerator;

class EntityConverter
{

    const RANGE_STEP = 100;
    const LAST_ENTITY_FIELD =  'VOID';
    const PRIMARY_ID = 'VOID';
    const VMA_LAST_ENTITY_TABLE = 'vma_last_entities';
    /**
     * @var QueryGenerator
     */
    private $queryGenerator;


    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;



    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Store\Model\StoreManagerInterface $storeManager = null
    ){
        $this->resource = $resource;
        if($storeManager) {
            $this->storeManager = $storeManager;
        }
        $this->connection = $resource->getConnection();
        $this->queryGenerator =  \Magento\Framework\App\ObjectManager::getInstance()
            ->get(QueryGenerator::class);
    }


    public static function getLastEntityField()
    {
        return self::LAST_ENTITY_FIELD;
    }


    public static function getFieldForLastEntity() {
        return static::getLastEntityField();
    }

    /**
     * Return validated table name
     *
     * @param string|string[] $table
     * @return string
     */
    protected function getTable($table)
    {
        return $this->resource->getTableName($table);
    }

    protected function prepareSelectsByRange(
        \Magento\Framework\DB\Select $select,
        $field
    )
    {
        $iterator = $this->queryGenerator->generate(
            $field,
            $select,
            self::RANGE_STEP,
            \Magento\Framework\DB\Query\BatchIteratorInterface::UNIQUE_FIELD_ITERATOR
        );

        $queries = [];
        foreach ($iterator as $query) {

            $queries[] = $query;
        }

        return $queries;
    }

    public  static function getPrimaryField()
    {
         return static::getEntityPrimaryField();
    }

    protected function saveLastEntityId($lastEntityId)
    {
        if(!empty($lastEntityId[static::getPrimaryField()])) {
            $this->connection->update(self::VMA_LAST_ENTITY_TABLE, [static::getFieldForLastEntity()=>$lastEntityId[static::getPrimaryField()]]);
        }
    }


    protected function getLastProcessedEntityId()
    {
        $lastUpdatedEntity = $this->connection
            ->select()
            ->from(self::VMA_LAST_ENTITY_TABLE, static::getFieldForLastEntity());

        $lastUpdatedEntityId = $this->connection->query($lastUpdatedEntity)->fetchColumn(0);
        return (int)$lastUpdatedEntityId;
    }
}