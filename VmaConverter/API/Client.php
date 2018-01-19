<?php
namespace Vma\VmaConverter\API;

use \Magento\Framework\Json\Helper\Data;

use Magento\Framework\Logger\Monolog;

class Client {

    protected $jsonHelper;
    protected $logger;

    public function __construct(Data $jsonHelper, Monolog $logger)
    {
       $this->jsonHelper = $jsonHelper;
       $this->logger     = $logger;
    }

    public function send($data)
    {
        $this->jsonHelper->jsonEncode($data);
    }
}