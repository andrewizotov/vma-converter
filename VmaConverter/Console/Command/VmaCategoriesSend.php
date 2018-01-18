<?php

namespace Vma\VmaConverter\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\ObjectManagerInterface;
use Vma\VmaConverter\Model\Converter\Category as CategoryConverter;

class VmaCategoriesSend extends Command {

    protected $categoryConverter;

    protected $objectManager;

    public function __construct(ObjectManagerInterface $objectManager, CategoryConverter $categoryConverter)
    {
        $this->objectManager = $objectManager;
        $this->categoryConverter = $categoryConverter;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('vma:categories_send')
            ->setDescription('Send categories to vma application.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $startTime = microtime(true);

        $this->categoryConverter->executeAll();

        $resultTime = microtime(true) - $startTime;

        $output->writeln(
            ' Categories has been sent successfully in ' . gmdate('H:i:s', $resultTime)
        );

        return \Magento\Framework\Console\Cli::RETURN_SUCCESS;
    }
}