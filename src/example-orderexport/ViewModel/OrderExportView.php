<?php
declare(strict_types = 1);
/**
 * @by SwiftOtter, Inc.
 * @website https://swiftotter.com
 **/

namespace SwiftOtter\OrderExport\ViewModel;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\View\Page\Config as PageConfig;
use SwiftOtter\OrderExport\Api\Data\OrderExportDetailsInterface;

class OrderExportView implements ArgumentInterface
{
    /**
     * @var OrderInterface|null
     */
    private $order = null;

    /**
     * @var RequestInterface
     */
    private $request;
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;
    /**
     * @var PageConfig
     */
    private $pageConfig;
    /**
     * @var UrlInterface
     */
    private $urlBuilder;
    /**
     * @var TimezoneInterface
     */
    private $localeDate;

    public function __construct(
        RequestInterface $request,
        OrderRepositoryInterface $orderRepository,
        PageConfig $pageConfig,
        UrlInterface $urlBuilder,
        TimezoneInterface $localeDate
    ) {
        $this->request = $request;
        $this->orderRepository = $orderRepository;
        $this->pageConfig = $pageConfig;
        $this->urlBuilder = $urlBuilder;
        $this->localeDate = $localeDate;

        $order = $this->getOrder();
        if ($order !== null) {
            $this->pageConfig->getTitle()->set(__('Order # %1 Export Details', $order->getIncrementId()));
        }
    }

    public function formatDate(\DateTime $date): string
    {
        return $this->localeDate->formatDate($date, \IntlDateFormatter::LONG);
    }

    public function getOrderViewUrl(): string
    {
        $order = $this->getOrder();
        if ($order === null) {
            return '';
        }
        return $this->urlBuilder->getUrl(
            'sales/order/view',
            [
                'order_id' => $order->getId(),
            ]
        );
    }

    public function getOrderExportDetails(): ?OrderExportDetailsInterface
    {
        $order = $this->getOrder();
        if ($order === null) {
            return null;
        }
        return $order->getExtensionAttributes()->getExportDetails();
    }

    public function getOrder(): ?OrderInterface
    {
        if ($this->order === null) {
            $orderId = (int) $this->request->getParam('order_id');
            if (!$orderId) {
                return null;
            }

            try {
                $order = $this->orderRepository->get($orderId);
            } catch (NoSuchEntityException $e) {
                return null;
            }

            $this->order = $order;
        }
        return $this->order;
    }
}
