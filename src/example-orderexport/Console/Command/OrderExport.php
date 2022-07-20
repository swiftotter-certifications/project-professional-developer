<?php
declare(strict_types = 1);
/**
 * @by SwiftOtter, Inc.
 * @website https://swiftotter.com
 **/

namespace SwiftOtter\OrderExport\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use SwiftOtter\OrderExport\Model\HeaderData;
use SwiftOtter\OrderExport\Model\HeaderDataFactory;
use SwiftOtter\OrderExport\Action\OrderExport as OrderExportAction;
use SwiftOtter\OrderExport\Model\Config;

class OrderExport extends Command
{
    const ARG_NAME_ORDER_ID = 'order-id';
    const OPT_NAME_SHIP_DATE = 'ship-date';
    const OPT_NAME_NOTES = 'notes';

    /**
     * @var HeaderDataFactory
     */
    private $headerDataFactory;
    /**
     * @var OrderExportAction
     */
    private $orderExport;
    /**
     * @var Config
     */
    private $config;

    public function __construct(
        HeaderDataFactory $headerDataFactory,
        OrderExportAction $orderExport,
        Config $config,
        string $name = null
    ) {
        parent::__construct($name);
        $this->headerDataFactory = $headerDataFactory;
        $this->orderExport = $orderExport;
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('order-export:run')
            ->setDescription('Export order to ERP')
            ->addArgument(
                self::ARG_NAME_ORDER_ID,
                InputArgument::REQUIRED,
                'Order ID'
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
        $orderId = $input->getArgument(self::ARG_NAME_ORDER_ID);
        $shipDate = $input->getOption(self::OPT_NAME_SHIP_DATE);
        $notes = $input->getOption(self::OPT_NAME_NOTES);

        /** @var HeaderData $headerData */
        $headerData = $this->headerDataFactory->create();
        if ($shipDate) {
            $headerData->setShipDate(new \DateTime($shipDate));
        }
        if ($notes) {
            $headerData->setMerchantNotes((string) $notes);
        }

        $result = $this->orderExport->run((int) $orderId, $headerData);
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
