<?php
declare(strict_types=1);
/**
 * @by SwiftOtter, Inc.
 * @website https://swiftotter.com
 **/

namespace SwiftOtter\OrderExport\Action;

use Magento\Framework\Exception\NoSuchEntityException;
use SwiftOtter\OrderExport\Model\HeaderData;

class ExportOrder
{
    /** @var CollectOrderData */
    private $orderDataCollector;

    /** @var PushDetailsToWebservice */
    private $pushDetailsToWebservice;

    /** @var SaveExportDetailsToOrder */
    private $saveExportDetailsToOrder;

    public function __construct(
        CollectOrderData         $orderDataCollector,
        PushDetailsToWebservice  $pushDetailsToWebservice,
        SaveExportDetailsToOrder $saveExportDetailsToOrder
    ) {
        $this->orderDataCollector = $orderDataCollector;
        $this->pushDetailsToWebservice = $pushDetailsToWebservice;
        $this->saveExportDetailsToOrder = $saveExportDetailsToOrder;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function execute(int $orderId, HeaderData $headerData): array
    {
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
