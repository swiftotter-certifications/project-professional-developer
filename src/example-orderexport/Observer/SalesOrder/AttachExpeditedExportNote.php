<?php
declare(strict_types = 1);
/**
 * @by SwiftOtter, Inc.
 * @website https://swiftotter.com
 **/

namespace SwiftOtter\OrderExport\Observer\SalesOrder;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Store\Model\ScopeInterface;
use SwiftOtter\OrderExport\Action\GetOrderExportItems;
use SwiftOtter\OrderExport\Api\Data\OrderExportDetailsInterface;
use SwiftOtter\OrderExport\Api\Data\OrderExportDetailsInterfaceFactory;
use SwiftOtter\OrderExport\Api\OrderExportDetailsRepositoryInterface;
use SwiftOtter\OrderExport\Model\Config;

class AttachExpeditedExportNote implements ObserverInterface
{
    const EXPEDITED_MSG = 'Order contains expedited items';

    /**
     * @var Config
     */
    private $config;
    /**
     * @var GetOrderExportItems
     */
    private $getOrderExportItems;
    /**
     * @var OrderExportDetailsInterfaceFactory
     */
    private $orderExportDetailsFactory;
    /**
     * @var OrderExportDetailsRepositoryInterface
     */
    private $orderExportDetailsRepository;

    public function __construct(
        Config $config,
        GetOrderExportItems $getOrderExportItems,
        OrderExportDetailsInterfaceFactory $orderExportDetailsFactory,
        OrderExportDetailsRepositoryInterface $orderExportDetailsRepository
    ) {
        $this->config = $config;
        $this->getOrderExportItems = $getOrderExportItems;
        $this->orderExportDetailsFactory = $orderExportDetailsFactory;
        $this->orderExportDetailsRepository = $orderExportDetailsRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Observer $observer)
    {
        try {
            /** @var OrderInterface $order */
            $order = $observer->getEvent()->getData('order');
            if (!$order) {
                return;
            }

            $expeditedSkus = $this->config->getExpeditedSkus(ScopeInterface::SCOPE_STORE, (string) $order->getStoreId());
            if (empty($expeditedSkus)) {
                return;
            }

            $expedited = false;
            foreach ($this->getOrderExportItems->execute($order) as $item) {
                if (in_array($item->getSku(), $expeditedSkus)) {
                    $expedited = true;
                    break;
                }
            }

            if ($expedited) {
                $orderExts = $order->getExtensionAttributes();
                $exportDetails = $orderExts->getExportDetails();
                if (!$exportDetails) {
                    /** @var OrderExportDetailsInterface $exportDetails */
                    $exportDetails = $this->orderExportDetailsFactory->create();
                    $exportDetails->setOrderId((int) $order->getEntityId());
                    $orderExts->setExportDetails($exportDetails);
                }

                $exportDetails->setMerchantNotes((string) __(self::EXPEDITED_MSG));
                $this->orderExportDetailsRepository->save($exportDetails);
            }
        } catch (\Throwable $e) {
            // TODO Logging or re-try behavior
        }
    }
}
