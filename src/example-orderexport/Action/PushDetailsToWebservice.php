<?php
declare(strict_types=1);
/**
 * @by SwiftOtter, Inc.
 * @website https://swiftotter.com
 **/

namespace SwiftOtter\OrderExport\Action;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Store\Model\ScopeInterface;
use SwiftOtter\OrderExport\Model\Config;
use GuzzleHttp\Client;

class PushDetailsToWebservice
{
    /** @var Config */
    private $config;

    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    public function execute(array $exportDetails, OrderInterface $order): bool
    {
        $apiUrl = $this->config->getApiUrl(ScopeInterface::SCOPE_STORE, $order->getStoreId());
        $apiToken = $this->config->getApiToken(ScopeInterface::SCOPE_STORE, $order->getStoreId());

        $client = new Client();
        $options = [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $apiToken,
            ],
            'body' => \json_encode($exportDetails),
        ];

        $client->post($apiUrl, $options);

        // TODO Make an HTTP request

        return true;
    }
}
