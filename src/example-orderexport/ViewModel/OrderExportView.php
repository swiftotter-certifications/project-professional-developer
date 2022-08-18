<?php
declare(strict_types=1);
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
use SwiftOtter\OrderExport\Api\Data\OrderExportDetailsInterface;
use Magento\Framework\View\Page\Config as PageConfig;

class OrderExportView implements ArgumentInterface
{
    /** @var null|OrderInterface */
    private $order = null;

    /** @var RequestInterface */
    private $request;
    /** @var OrderRepositoryInterface */
    private $orderRepository;
    /** @var TimezoneInterface */
    private $timezone;
    /** @var UrlInterface */
    private $urlBuilder;
    /** @var PageConfig */
    private $pageConfig;

    public function __construct(
        RequestInterface $request,
        OrderRepositoryInterface $orderRepository,
        TimezoneInterface $timezone,
        UrlInterface $urlBuilder,
        PageConfig $pageConfig
    ) {

        $this->request = $request;
        $this->orderRepository = $orderRepository;
        $this->timezone = $timezone;
        $this->urlBuilder = $urlBuilder;
        $this->pageConfig = $pageConfig;

        $order = $this->getOrder();
        if ($order) {
            $this->pageConfig->getTitle()->set(__('Order # %1', $order->getRealOrderId()));
        }
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
            $orderId = (int)$this->request->getParam('order_id');
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

    public function formatDate(\DateTime $dateTime): string
    {
        return $this->timezone->formatDate($dateTime, \IntlDateFormatter::LONG);
    }

    public function getOrderViewUrl(): string
    {
        $order = $this->getOrder();
        if (!$order) {
            return '';
        }

        return $this->urlBuilder->getUrl(
            'sales/order/view',
            [
                'order_id' => $order->getEntityId()
            ]
        );
    }
}
