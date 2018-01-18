<?php

namespace Vma\VmaConverter\Helper;

use Vma\VmaConverter\Helper\Entity\OrderHelper;
use Vma\VmaConverter\Helper\Entity\ProductHelper;
use Vma\VmaConverter\Helper\Entity\CategoryHelper;
use Magento\Framework\ObjectManagerInterface;


class Data
{
    protected $productHelper;
    protected $categoryHelper;
    protected $objectManager;
    protected $orderHelper;

    function __construct(
        ObjectManagerInterface $objectManager,
        ProductHelper $productHelper,
        CategoryHelper $categoryHelper,
        OrderHelper  $orderHelper
    )
    {
        $this->objectManager = $objectManager;
        $this->productHelper  = $productHelper;
        $this->categoryHelper  = $categoryHelper;
        $this->orderHelper  = $orderHelper;
    }

    public function getProductsRecords($collection)
    {
        $productsToIndex = [];


        /** @var Product $product */
        foreach ($collection as $product) {
            $productId = $product->getId();

            $productsToIndex[$productId] = $this->productHelper->getObject($product);
        }

        return [
            'toIndex' => $productsToIndex,
            'toRemove' => array()
        ];
    }

    public function getCategoriesRecords($collection)
    {
        $categoriesToIndex = [];


        /** @var Category $product */
        foreach ($collection as $category) {

            $categoryId = $category->getId();

            $categoriesToIndex[$categoryId] = $this->categoryHelper->getObject($category);
        }

        return [
            'toIndex' => $categoriesToIndex,
            'toRemove'=> array()
        ];
    }


    public function getOrdersRecords($collection)
    {
        $ordersToIndex = [];


        foreach ($collection as $order) {

            $orderId = $order['entity_id'];

            $ordersToIndex[$orderId] = $this->orderHelper->getObject($order);
        }

        return $ordersToIndex;
    }
}
