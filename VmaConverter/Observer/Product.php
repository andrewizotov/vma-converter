<?php

namespace Vma\VmaConverter\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Vma\VmaConverter\Model\Converter\Product as ProductConverter;

class Product implements ObserverInterface
{

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    protected $productConverter;
    protected $apiClient;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        ProductConverter $productConverter,
        \Vma\VmaConverter\API\Client  $apiClient
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->productConverter = $productConverter;
        $this->apiClient = $apiClient;
    }

    public function execute(Observer $observer)
    {
        $product = $observer->getProduct();

        $data =  $this->productConverter->executeOne($product->getId());

        $this->apiClient->send($data);
    }
}