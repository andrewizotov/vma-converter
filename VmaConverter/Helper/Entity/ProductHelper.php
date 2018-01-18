<?php

namespace Vma\VmaConverter\Helper\Entity;

use Magento\Framework\ObjectManagerInterface;
use Magento\Catalog\Model\Product\Attribute\Frontend\Image;

class ProductHelper extends BaseEntityHelper
{

    protected $imageAttribute;

    public function __construct(
        ObjectManagerInterface $objectManager,
        Image $baseImageAttribute
    )
    {
        $this->imageAttribute = $baseImageAttribute;
        parent::__construct($objectManager);
    }

    public function getProductCollectionQuery($productIds = null, $additionalAttributes = array())
    {

        /** @var $products \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection */
        $products = $this->objectManager->create('Magento\Catalog\Model\ResourceModel\Product\Collection');

        $products = $products
            ->addAttributeToSelect('special_from_date')
            ->addAttributeToSelect('special_to_date')
            ->addAttributeToSelect('visibility')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('brand','left');

        if (!empty($additionalAttributes)){
            foreach ($additionalAttributes as $attr){
                $products->addAttributeToSelect($attr,'left');
            }
        }

        if ($productIds && count($productIds) > 0) {
            $products = $products->addAttributeToFilter('entity_id', ['in' => $productIds]);
        }

        return $products;
    }

    public function getObject($product)
    {
        $type = $product->getTypeId();

        $brand = $product->getResource()->getAttribute('brand');
        $optionTextBrand = $brand->getSource()->getOptionText($product->getBrand());

        $images = $this->getProductImages($product);

        $data = [
               'id'             => $product->getId(),
               'title'          => $product->getName(),
               'body_html'      => '',
               'vendor'         => $optionTextBrand,
               'product_type'   => $type,
               'created_at'     => $product->getCreatedAt(),
               'handle'         =>'',
               'updated_at'     => $product->getUpdatedAt(),
               'published_at'   => '',
               'template_suffix'=>'',
               'published_scope'=> '',
               'tags'           => '',
               'variants'       => array(),
               'options'        =>'',
               'images'         => $images,
               'image'          =>''
        ];


        if ($type == 'configurable' || $type == 'grouped' || $type == 'bundle') {

            if ($type == 'configurable' || $type == 'grouped') {
                $ids = $product->getTypeInstance(true)->getChildrenIds($product->getId());
                $ids = call_user_func_array('array_merge', $ids);
            }

            if (count($ids)) {
                $sub_products = $this->getProductCollectionQuery($ids, ['color']);

                foreach ($sub_products as $sub_product){

                    $subData = [
                        "product_id" => $sub_product->getId(),
                        "sku"        => $sub_product->getSku(),
                        "title"      => $this->getAttributeColor($sub_product, $sub_product->getColor())
                    ];
                    $data['variants'][] = $subData;
                }
            }
        }


        return $data;
    }

    public function getProductImages($product)
    {

    }

    public function getAttributeColor($product, $optionId)
    {
        $color = $product->getResource()->getAttribute('color');
        $optionTextColor = $color->getSource()->getOptionText($product->getColor());
        return $optionTextColor;
    }
}