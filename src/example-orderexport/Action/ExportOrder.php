<?php
declare(strict_types=1);
/**
 * @by SwiftOtter, Inc.
 * @website https://swiftotter.com
 **/

namespace SwiftOtter\OrderExport\Action;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use SwiftOtter\OrderExport\Model\HeaderData;
use SwiftOtter\OrderExport\Model\Config;

class ExportOrder
{
    /** @var CollectOrderData */
    private $orderDataCollector;

    /** @var PushDetailsToWebservice */
    private $pushDetailsToWebservice;

    /** @var SaveExportDetailsToOrder */
    private $saveExportDetailsToOrder;

    /** @var Config */
    private $config;

    public function __construct(
        CollectOrderData         $orderDataCollector,
        PushDetailsToWebservice  $pushDetailsToWebservice,
        SaveExportDetailsToOrder $saveExportDetailsToOrder,
        Config                   $config
    ) {
        $this->orderDataCollector = $orderDataCollector;
        $this->pushDetailsToWebservice = $pushDetailsToWebservice;
        $this->saveExportDetailsToOrder = $saveExportDetailsToOrder;
        $this->config = $config;
    }

    /**
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function execute(int $orderId, HeaderData $headerData): array
    {
        if (!$this->config->isEnabled()) {
            throw new LocalizedException(__('Order export is disabled'));
        }

        $results = ['success' => false, 'error' => null];

        $exportData = $this->orderDataCollector->execute($orderId, $headerData);

        try {
            $results['success'] = $this->pushDetailsToWebservice->execute($exportData);
            $this->saveExportDetailsToOrder->execute($orderId, $headerData, $results);
        } catch (\Throwable $ex) {
            $results['error'] = $ex->getMessage();
        }

        return $results;
    }
}
