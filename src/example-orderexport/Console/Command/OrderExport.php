<?php
declare(strict_types = 1);
/**
 * @by SwiftOtter, Inc.
 * @website https://swiftotter.com
 **/

namespace SwiftOtter\OrderExport\Console\Command;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\OrderRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use SwiftOtter\OrderExport\Model\HeaderData;
use SwiftOtter\OrderExport\Model\HeaderDataFactory;
use SwiftOtter\OrderExport\Action\OrderExport as OrderExportAction;
use Magento\Sales\Api\Data\OrderInterface;

class OrderExport extends Command
{
    const ARG_NAME_ORDER_NUM = 'order-num';
    const OPT_NAME_SHIP_DATE = 'ship-date';
    const OPT_NAME_NOTES = 'notes';

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var HeaderDataFactory
     */
    private $headerDataFactory;
    /**
     * @var OrderExportAction
     */
    private $orderExport;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        HeaderDataFactory $headerDataFactory,
        OrderExportAction $orderExport,
        string $name = null
    ) {
        parent::__construct($name);
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->headerDataFactory = $headerDataFactory;
        $this->orderExport = $orderExport;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('order-export:run')
            ->setDescription('Export order to ERP')
            ->addArgument(
                self::ARG_NAME_ORDER_NUM,
                InputArgument::REQUIRED,
                'Order increment number'
            )
            ->addOption(
                self::OPT_NAME_SHIP_DATE,
                'd',
                InputOption::VALUE_OPTIONAL,
                'Shipping date in format YYYY-MM-DD'
            )
            ->addOption(
                self::OPT_NAME_NOTES,
                null,
                InputOption::VALUE_OPTIONAL,
                'Merchant notes'
            );

        parent::configure();
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $orderNum = $input->getArgument(self::ARG_NAME_ORDER_NUM);
        $shipDate = $input->getOption(self::OPT_NAME_SHIP_DATE);
        $notes = $input->getOption(self::OPT_NAME_NOTES);

        $this->searchCriteriaBuilder->addFilter('increment_id', $orderNum);
        $orders = $this->orderRepository->getList($this->searchCriteriaBuilder->create())->getItems();

        if (count($orders) <= 0) {
            $output->writeln(__('Order not found'));
            return 1;
        }
        /** @var OrderInterface $order */
        $order = reset($orders);

        /** @var HeaderData $headerData */
        $headerData = $this->headerDataFactory->create();
        if ($shipDate) {
            $headerData->setShipDate(new \DateTime($shipDate));
        }
        if ($notes) {
            $headerData->setMerchantNotes((string) $notes);
        }

        $result = $this->orderExport->run((int) $order->getId(), $headerData);
        $success = $result['success'] ?? false;
        if ($success) {
            $output->writeln(__('Successfully exported order'));
        } else {
            $msg = $result['error'] ?? null;
            if ($msg === null) {
                $msg = __('Unexpected errors occurred');
            }
            $output->writeln($msg);
            return 1;
        }

        return 0;
    }
}
