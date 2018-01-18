<?php

namespace Vma\VmaConverter\Model\Converter;

use Vma\VmaConverter\Helper\Data;
use Magento\Framework\ObjectManagerInterface;
use Vma\VmaConverter\Helper\Entity\OrderHelper;
use Vma\VmaConverter\API\Client;

class Order extends EntityConverter
{
    const RANGE_STEP = 100;
    const MAIN_ORDER_TABLE = 'sales_order';

    protected $orderHelper;
    protected $helper;
    protected $apiClient;

    /**
     * Cached all product select by store id
     *
     * @var \Magento\Framework\DB\Select[]
     */
    protected $ordersSelects = [];


    function __construct(
        ObjectManagerInterface $objectManager,
        OrderHelper $orderHelper,
        Data $helper,
        Client $apiClient
    )
    {
        $this->apiClient = $apiClient;
        $this->objectMaganer = $objectManager;
        $this->orderHelper = $orderHelper;
        $this->helper = $helper;

        /* @var $resource \Magento\Framework\App\ResourceConnection */
        $resource = $this->objectMaganer->get('\Magento\Framework\App\ResourceConnection');

        parent::__construct($resource, null);
    }

    public function executeAll()
    {
        $selects = $this->prepareSelectsByRange(
            $this->getAllOrders(),
            'entity_id',
            self::RANGE_STEP
        );

        foreach ($selects as $select) {
            $res = $this->connection->query($select->__toString());

            $orderIds = $res->fetchAll(\Zend_Db::FETCH_COLUMN, 0);

            if (count($orderIds) > 0) {
                $collection = $this->orderHelper->getOrdersCollection($orderIds);
                $records = $this->helper->getOrdersRecords($collection);
                $this->apiClient->send($records);
            }
        }
    }

    protected function getAllOrders()
    {
        $select = $this->connection->select()->from(
            self::MAIN_ORDER_TABLE, ['entity_id']
        );

        return $this->productsSelects[] = $select;

    }

}