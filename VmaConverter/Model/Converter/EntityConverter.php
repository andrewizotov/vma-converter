<?php

namespace Vma\VmaConverter\Model\Converter;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Query\Generator as QueryGenerator;

class EntityConverter
{

    const RANGE_STEP = 100;

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
}