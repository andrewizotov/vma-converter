<?php

namespace Vma\VmaConverter\Helper\Entity;

use Magento\Framework\ObjectManagerInterface;

class OrderHelper extends BaseEntityHelper
{

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    public function __construct(ObjectManagerInterface $objectManager)
    {
        parent::__construct($objectManager);
    }

    public function getOrdersCollection($orderIds = null)
    {
        if(count($orderIds) == 0 ){
            return null;
        }

        /* @var $resource \Magento\Framework\App\ResourceConnection */
        $resource = $this->objectManager->get('\Magento\Framework\App\ResourceConnection');
        /* */
        $this->connection = $resource->getConnection();
        $select =  $this->connection
            ->query(
                "SELECT * FROM sales_order WHERE entity_id IN (".implode(',',$orderIds).")"
            );

        return $select->fetchAll();
    }

    public function getObject($order)
    {

        $orderObject =  new \Magento\Framework\DataObject();
        $order = $orderObject->addData($order);

        $lineItems = $this->getLineItems($order->getEntityId());

        $data = [
            'id'          => $order->getId(),
            'email'       => $order->getCustomerEmail(),
            'closed_at'   => $order->getUpdatedAt(),
            'created_at'  => $order->getCreatedAt(),
            'updated_at'  => $order->getUpdatedAt(),
            'number'      => $order->getIncrementId(),
            'note'        => '',
            'token'       => '',
            'gateway'     => '',
            'test'        => false,
            'total_price' => $order->getGrandTotal(),
            'subtotal_price'=>  $order->getSubTotal(),
            'total_weight' =>  $order->getWeight(),
            'total_tax'    => $order->getBaseTaxAmount(),
            'taxes_included' => false,
            'currency' => $order->getBaseCurrencyCode(),
            'financial_status' => $order->getStatus(),
            'discount_codes' => [],
            'line_items' => $lineItems
        ];

        unset($orderObject);
        return $data;
    }

    public function getItemObject($orderItem)
    {

        $orderItemObject =  new \Magento\Framework\DataObject();
        $orderItem = $orderItemObject->addData($orderItem);

        $data = [
            'id'          => $orderItem->getItemId(),
            'variant_id'  => $orderItem->getProductId(),
            'title'       => $orderItem->getName(),
            'quantity'    => $orderItem->getQtyOrdered(),
            'price'       =>  $orderItem->getPrice(),
        ];

        unset($orderItemObject);
        return $data;
    }

    protected function getLineItems($orderId)
    {
       $res = $this->connection
           ->query(
               "SELECT * FROM `{$this->connection->getTableName('sales_order_item')}` WHERE order_id={$orderId}"
           );

       $items = $res->fetchAll();
       $orderItems = [];
       foreach ($items as $item) {
           $orderItems[] = $this->getItemObject($item);
       }

       return $orderItems;
    }
}