<?php
namespace Vma\VmaConverter\API;

use \Magento\Framework\Json\Helper\Data;

class Client {

    protected $jsonHelper;

    public function __construct(Data $jsonHelper)
    {
       $this->jsonHelper = $jsonHelper;
    }

    public function send($data){

        //var_dump($this->jsonHelper->jsonEncode($data));

    }
}