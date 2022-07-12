<?php
declare(strict_types = 1);
/**
 * @by SwiftOtter, Inc.
 * @website https://swiftotter.com
 **/

namespace SwiftOtter\OrderExport\Controller\View;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Forward;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Controller\AbstractController\OrderViewAuthorizationInterface;
use Magento\Sales\Model\Order;

class Index implements ActionInterface
{
    /**
     * @var PageFactory
     */
    private $pageFactory;
    /**
     * @var RequestInterface
     */
    private $request;
    /**
     * @var ForwardFactory
     */
    private $forwardFactory;
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;
    /**
     * @var OrderViewAuthorizationInterface
     */
    private $orderAuthorization;
    /**
     * @var RedirectFactory
     */
    private $redirectFactory;
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    public function __construct(
        PageFactory $pageFactory,
        RequestInterface $request,
        ForwardFactory $forwardFactory,
        OrderRepositoryInterface $orderRepository,
        OrderViewAuthorizationInterface $orderAuthorization,
        RedirectFactory $redirectFactory,
        UrlInterface $urlBuilder
    ) {
        $this->pageFactory = $pageFactory;
        $this->request = $request;
        $this->forwardFactory = $forwardFactory;
        $this->orderRepository = $orderRepository;
        $this->orderAuthorization = $orderAuthorization;
        $this->redirectFactory = $redirectFactory;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $orderId = (int) $this->request->getParam('order_id');
        /** @var Forward $forward */
        $forward = $this->forwardFactory->create();
        if (!$orderId) {
            return $forward->forward('noroute');
        }

        try {
            /** @var OrderInterface|Order $order */
            $order = $this->orderRepository->get($orderId);
        } catch (NoSuchEntityException $e) {
            return $forward->forward('noroute');
        }

        /**
         * TODO Since OrderViewAuthorizationInterface expects Order model instead of relying on OrderInterface,
         * we should do a type check
         */
        if (!$this->orderAuthorization->canView($order)) {
            /** @var Redirect $redirect */
            $redirect = $this->redirectFactory->create();
            return $redirect->setUrl($this->urlBuilder->getUrl('sales/order/history'));
        }

        /** @var Page $resultPage */
        $resultPage = $this->pageFactory->create();
        return $resultPage;
    }
}
