<?php

namespace Vma\VmaConverter\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\ObjectManagerInterface;
use Vma\VmaConverter\Model\Converter\Product as ProductConverter;

class VmaProductsSend extends Command
{

    protected $productConverter;
    protected $objectManager;

    public function __construct(ObjectManagerInterface $objectManager, ProductConverter $productConverter)
    {
        $this->objectManager = $objectManager;
        $this->productConverter = $productConverter;

        parent::__construct();
    }

    protected function configure()
    {

        $this->setName('vma:products_send')
            ->setDescription('Send products to vma application.');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $startTime = microtime(true);

        $this->productConverter->executeAll();

        $resultTime = microtime(true) - $startTime;

        $output->writeln(
            ' Products has been sent successfully in ' . gmdate('H:i:s', $resultTime)
        );

        return \Magento\Framework\Console\Cli::RETURN_SUCCESS;
    }
}