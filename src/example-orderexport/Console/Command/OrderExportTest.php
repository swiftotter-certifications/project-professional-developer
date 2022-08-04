<?php
declare(strict_types=1);
/**
 * @by SwiftOtter, Inc.
 * @website https://swiftotter.com
 **/

namespace SwiftOtter\OrderExport\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use SwiftOtter\OrderExport\Model\ResourceModel\OrderExportDetails\Collection as OrderExportDetailsCollection;
use SwiftOtter\OrderExport\Model\ResourceModel\OrderExportDetails\CollectionFactory as OrderExportDetailsCollectionFactory;

class OrderExportTest extends Command
{
    /** @var OrderExportDetailsCollectionFactory */
    private $orderExportDetailsCollectionFactory;

    public function __construct(
        OrderExportDetailsCollectionFactory $orderExportDetailsCollectionFactory,
        string $name = null
    ) {
        parent::__construct($name);
        $this->orderExportDetailsCollectionFactory = $orderExportDetailsCollectionFactory;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('order-export:test')
            ->setDescription('Test various order export features');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $exportDetailsCollection = $this->orderExportDetailsCollectionFactory->create();
        foreach ($exportDetailsCollection as $exportDetails) {
            $output->writeln(print_r($exportDetails->getData(), true));
        }

        return 0;
    }
}
