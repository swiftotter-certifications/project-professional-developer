<?php
declare(strict_types=1);
/**
 * @by SwiftOtter, Inc.
 * @website https://swiftotter.com
 **/

namespace SwiftOtter\OrderExport\Action;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Store\Model\ScopeInterface;
use SwiftOtter\OrderExport\Model\HeaderData;
use SwiftOtter\OrderExport\Model\Config;
use Magento\Framework\Exception\NoSuchEntityException;
use SwiftOtter\OrderExport\Action\PushDetailsToWebservice;

class ExportOrder
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;
    /** @var Config */
    private $config;
    /** @var CollectOrderData */
    private $collectOrderData;
    /** @var \SwiftOtter\OrderExport\Action\PushDetailsToWebservice */
    private $pushDetailsToWebservice;
    /** @var SaveExportDetailsToOrder */
    private $saveExportDetailsToOrder;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        Config $config,
        CollectOrderData $collectOrderData,
        PushDetailsToWebservice $pushDetailsToWebservice,
        SaveExportDetailsToOrder $saveExportDetailsToOrder
    ) {
        $this->orderRepository = $orderRepository;
        $this->config = $config;
        $this->collectOrderData = $collectOrderData;
        $this->pushDetailsToWebservice = $pushDetailsToWebservice;
        $this->saveExportDetailsToOrder = $saveExportDetailsToOrder;
    }

    /**
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute(int $orderId, HeaderData $headerData): array
    {
        $order = $this->orderRepository->get($orderId);

        if (!$this->config->isEnabled(ScopeInterface::SCOPE_STORE, $order->getStoreId())) {
            throw new LocalizedException(__('Order export is disabled'));
        }

        $results = ['success' => false, 'error' => null];

        $exportData = $this->collectOrderData->execute($order, $headerData);

        try {
            $results['success'] = $this->pushDetailsToWebservice->execute($exportData, $order);
            $this->saveExportDetailsToOrder->execute($order, $headerData, $results);
        } catch (\Throwable $ex) {
            $results['error'] = $ex->getMessage();
        }

        return $results;
    }
}
