<?php
declare(strict_types=1);
/**
 * @by SwiftOtter, Inc.
 * @website https://swiftotter.com
 **/

namespace SwiftOtter\OrderExport\Console\Command;

use Magento\Framework\Api\SearchCriteriaBuilder;
use SwiftOtter\OrderExport\Api\OrderExportDetailsRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class OrderExportTest extends Command
{
    /** @var SearchCriteriaBuilder */
    private $searchCriteriaBuilder;
    /** @var OrderExportDetailsRepositoryInterface */
    private $exportDetailsRepository;

    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        OrderExportDetailsRepositoryInterface $exportDetailsRepository,
        string $name = null
    ) {
        parent::__construct($name);
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
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
        $this->searchCriteriaBuilder->addFilter('order_id', 1);
        $exportDetails = $this->exportDetailsRepository->getList($this->searchCriteriaBuilder->create())->getItems();
        foreach ($exportDetails as $exportDetailsRecord) {
            $output->writeln(print_r($exportDetailsRecord->getData(), true));
        }

        return 0;
    }
}
