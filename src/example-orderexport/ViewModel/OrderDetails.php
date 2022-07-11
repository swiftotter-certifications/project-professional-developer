<?php
declare(strict_types=1);
/**
 * @by SwiftOtter, Inc. 12/28/19
 * @website https://swiftotter.com
 **/

namespace SwiftOtter\OrderExport\ViewModel;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class OrderDetails implements ArgumentInterface
{
    /** @var AuthorizationInterface */
    private $authorization;

    /** @var UrlInterface */
    private $urlBuilder;

    /** @var FormKey */
    private $formKey;

    /** @var RequestInterface */
    private $request;

    public function __construct(
        AuthorizationInterface $authorization,
        UrlInterface $urlBuilder,
        FormKey $formKey,
        RequestInterface $request
    ) {
        $this->authorization = $authorization;
        $this->urlBuilder = $urlBuilder;
        $this->formKey = $formKey;
        $this->request = $request;
    }

    public function isAllowed(): bool
    {
        return $this->authorization->isAllowed('SwiftOtter_OrderExport::OrderExport');
    }

    public function getButtonMessage(): string
    {
        return (string)__('Send Order to Fulfillment');
    }

    public function getConfig(): array
    {
        return [
            'sending_message' => __('Sending...'),
            'original_message' => $this->getButtonMessage(),
            'upload_url' => $this->urlBuilder->getUrl(
                'order_export/export/run',
                [
                    'order_id' => (int)$this->request->getParam('order_id')
                ]
            ),
            'form_key' => $this->formKey->getFormKey()
        ];
    }
}