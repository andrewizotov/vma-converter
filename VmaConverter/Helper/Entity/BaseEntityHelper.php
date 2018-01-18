<?php

namespace Vma\VmaConverter\Helper\Entity;

use Magento\Framework\ObjectManagerInterface;

class BaseEntityHelper
{
   protected $objectManager;

   public function __construct(ObjectManagerInterface $objectManager)
   {
      $this->objectManager = $objectManager;
   }

}