<?php
declare(strict_types=1);
/**
 * @by SwiftOtter, Inc.
 * @website https://swiftotter.com
 **/

namespace SwiftOtter\OrderExport\Action;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;
use SwiftOtter\OrderExport\Api\Data\OrderExportDetailsInterface;
use SwiftOtter\OrderExport\Api\Data\OrderExportDetailsInterfaceFactory;
use SwiftOtter\OrderExport\Api\OrderExportDetailsRepositoryInterface;
use SwiftOtter\OrderExport\Model\Config;

class AttachExpeditedExportNote
{
    const EXPEDITED_MSG = 'Order contains expedited items';

    /** @var Config */
    private $config;
    /** @var GetOrderExportItems */
    private $getOrderExportItems;
    /** @var OrderExportDetailsInterfaceFactory */
    private $exportDetailsFactory;
    /** @var OrderExportDetailsRepositoryInterface */
    private $exportDetailsRepository;
    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        Config $config,
        GetOrderExportItems $getOrderExportItems,
        OrderExportDetailsInterfaceFactory $exportDetailsFactory,
        OrderExportDetailsRepositoryInterface $exportDetailsRepository,
        LoggerInterface $logger
    ) {
        $this->config = $config;
        $this->getOrderExportItems = $getOrderExportItems;
        $this->exportDetailsFactory = $exportDetailsFactory;
        $this->exportDetailsRepository = $exportDetailsRepository;
        $this->logger = $logger;
    }

    /**
     * @throws LocalizedException
     */
    public function execute(OrderInterface $order): bool
    {
        if ($this->config->isEnabled(ScopeInterface::SCOPE_STORE, $order->getStoreId())) {
            return true;
        }

        $expeditedSkus = $this->config->getExpeditedSkus(ScopeInterface::SCOPE_STORE, (string) $order->getStoreId());
        if (empty($expeditedSkus)) {
            return true;
        }

        $expedited = false;
        foreach ($this->getOrderExportItems->execute($order) as $item) {
            if (in_array($item->getSku(), $expeditedSkus)) {
                $expedited = true;
                break;
            }
        }

        if ($expedited) {
            try {
                $orderExts = $order->getExtensionAttributes();
                /** @var OrderExportDetailsInterface $exportDetails */
                $exportDetails = $orderExts->getExportDetails();
                if (!$exportDetails) {
                    $exportDetails = $this->exportDetailsFactory->create();
                    $exportDetails->setOrderId((int)$order->getEntityId());
                    $orderExts->setExportDetails($exportDetails);
                }

                $exportDetails->setMerchantNotes((string)__(self::EXPEDITED_MSG));
                $this->exportDetailsRepository->save($exportDetails);
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
                // TODO Re-try logic or error e-mail?
                throw new LocalizedException(__('Expedited note could not be saved for order #%1', $order->getIncrementId()));
            }
        }

        return true;
    }
}
