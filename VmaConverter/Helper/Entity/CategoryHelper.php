<?php

namespace Vma\VmaConverter\Helper\Entity;
use Magento\Framework\ObjectManagerInterface;

class CategoryHelper extends BaseEntityHelper
{

    public function __construct(ObjectManagerInterface $objManager)
    {
        parent::__construct($objManager);
    }

    public function getCategoryCollectionQuery($categoryIds = null)
    {
        if (empty($categoryIds)) {
            return null;
        }

        /* @var \Magento\Catalog\Model\ResourceModel\Category\Collection $categories */
        $categories = $this->objectManager->create('Magento\Catalog\Model\ResourceModel\Category\Collection');

        $categories
            ->addNameToResult()
            ->addAttributeToSelect('description')
            ->addAttributeToSelect('image');

        $categories->addFieldToFilter('entity_id', ['in' => $categoryIds]);

        //var_dump($categories->getSelectSql()->__toString());
        return $categories;
    }


    public function getObject($category)
    {
        $image = $this->getImageInfo($category);
        $data = [
            'collection_id' => $category->getId(),
            'updated_at' => $category->getUpdatedAt(),
            'body_html' => $category->getDescription(),
            'default_product_image' => $image,
            'handle' => '',
            'image' => [],
            'title' => $category->getName(),
            'sort_order' => '',
            'published_at' => $category->getCreatedAt(),
        ];


        return $data;
    }

    protected function getImageInfo($category)
    {
        $img = $category->getResource()->getAttribute('image');

    }

}