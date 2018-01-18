<?php

namespace Vma\VmaConverter\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\ObjectManagerInterface;
use Vma\VmaConverter\Model\Converter\Order as OrderConverter;

class VmaOrdersSend extends Command {

    protected $orderConverter;
    protected $objectManager;

    public function __construct(ObjectManagerInterface $objectManager, OrderConverter $orderConverter)
    {
        $this->objectManager = $objectManager;
        $this->orderConverter = $orderConverter;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('vma:orders_send')
            ->setDescription('Send orders to vma application.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $startTime = microtime(true);

        $this->orderConverter->executeAll();

        $resultTime = microtime(true) - $startTime;

        $output->writeln(
            ' Orders has been sent successfully in ' . gmdate('H:i:s', $resultTime)
        );

        return \Magento\Framework\Console\Cli::RETURN_SUCCESS;
    }
}