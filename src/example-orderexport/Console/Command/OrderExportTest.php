<?php
declare(strict_types=1);
/**
 * @by SwiftOtter, Inc.
 * @website https://swiftotter.com
 **/

namespace SwiftOtter\OrderExport\Console\Command;

use SwiftOtter\OrderExport\Api\OrderExportDetailsRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class OrderExportTest extends Command
{
    /** @var OrderExportDetailsRepositoryInterface */
    private $exportDetailsRepository;

    public function __construct(
        OrderExportDetailsRepositoryInterface $exportDetailsRepository,
        string $name = null
    ) {
        parent::__construct($name);
        $this->exportDetailsRepository = $exportDetailsRepository;
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
        $exportDetails = $this->exportDetailsRepository->getById(1);
        $output->writeln(print_r($exportDetails->getData(), true));

        return 0;
    }
}
