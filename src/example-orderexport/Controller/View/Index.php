<?php
declare(strict_types=1);
/**
 * @by SwiftOtter, Inc.
 * @website https://swiftotter.com
 **/

namespace SwiftOtter\OrderExport\Controller\View;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Controller\AbstractController\OrderViewAuthorizationInterface;
use Magento\Framework\Controller\Result\Forward;
use Magento\Framework\Controller\Result\ForwardFactory;
use SwiftOtter\OrderExport\Model\Config;

class Index implements ActionInterface, HttpGetActionInterface
{
    /** @var PageFactory */
    private $pageFactory;
    /** @var OrderViewAuthorizationInterface */
    private $orderViewAuthorization;
    /** @var RequestInterface */
    private $request;
    /** @var OrderRepositoryInterface */
    private $orderRepository;
    /** @var ForwardFactory */
    private $forwardFactory;
    /** @var Config */
    private $config;

    public function __construct(
        PageFactory $pageFactory,
        OrderViewAuthorizationInterface $orderViewAuthorization,
        RequestInterface $request,
        OrderRepositoryInterface $orderRepository,
        ForwardFactory $forwardFactory,
        Config $config
    ) {
        $this->pageFactory = $pageFactory;
        $this->orderViewAuthorization = $orderViewAuthorization;
        $this->request = $request;
        $this->orderRepository = $orderRepository;
        $this->forwardFactory = $forwardFactory;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var Forward $forward */
        $forward = $this->forwardFactory->create();

        if (!$this->config->isEnabled()) {
            return $forward->forward('noroute');
        }

        $orderId = (int) $this->request->getParam('order_id');
        if (!$orderId) {
            return $forward->forward('noroute');
        }

        try {
            $order = $this->orderRepository->get($orderId);
        } catch (\Exception $e) {
            return $forward->forward('noroute');
        }

        if (!$this->orderViewAuthorization->canView($order)) {
            return $forward->forward('noroute');
        }

        /** @var Page $pageResult */
        $pageResult = $this->pageFactory->create();

        return $pageResult;
    }
}
