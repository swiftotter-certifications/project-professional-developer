<?php
declare(strict_types=1);
/**
 * @by SwiftOtter, Inc.
 * @website https://swiftotter.com
 **/

namespace SwiftOtter\OrderExport\Console\Command;

use SwiftOtter\OrderExport\Api\Data\OrderExportDetailsInterface;
use SwiftOtter\OrderExport\Api\Data\OrderExportDetailsInterfaceFactory;
use SwiftOtter\OrderExport\Model\ResourceModel\OrderExportDetails;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class OrderExportTest extends Command
{
    /** @var OrderExportDetailsInterfaceFactory */
    private $orderExportDetailsFactory;
    /** @var OrderExportDetails */
    private $orderExportDetailsResource;

    public function __construct(
        OrderExportDetailsInterfaceFactory $orderExportDetailsFactory,
        OrderExportDetails $orderExportDetailsResource,
        string $name = null
    ) {
        parent::__construct($name);
        $this->orderExportDetailsFactory = $orderExportDetailsFactory;
        $this->orderExportDetailsResource = $orderExportDetailsResource;
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
        $exportDetails = $this->orderExportDetailsFactory->create();

        // Prerequisite: Create a record with ID 1 in sales_order_export
        $this->orderExportDetailsResource->load($exportDetails, 1);

        $output->writeln(print_r($exportDetails->getData(), true));

        return 0;
    }
}
