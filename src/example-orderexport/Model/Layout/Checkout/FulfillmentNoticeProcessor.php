<?php
declare(strict_types=1);
/**
 * @by SwiftOtter, Inc.
 * @website https://swiftotter.com
 **/

namespace SwiftOtter\OrderExport\Model\Layout\Checkout;

use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use SwiftOtter\OrderExport\Model\Config;

class FulfillmentNoticeProcessor implements LayoutProcessorInterface
{
    /** @var Config */
    private $config;

    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function process($jsLayout)
    {
        if (!$this->config->isEnabled()) {
            return $jsLayout;
        }

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['fulfillmentNotice']['config']['noticeContent'] = __('Hello World from a LayoutProcessor');

        return $jsLayout;
    }
}
