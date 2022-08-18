<?php
declare(strict_types=1);
/**
 * @by SwiftOtter, Inc.
 * @website https://swiftotter.com
 **/

namespace SwiftOtter\OrderExport\Action;

use GuzzleHttp\Exception\GuzzleException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;
use SwiftOtter\OrderExport\Model\Config;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class PushDetailsToWebservice
{
    /** @var Config */
    private $config;
    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        Config $config,
        LoggerInterface $logger
    ) {
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * @throws GuzzleException
     * @throws LocalizedException
     */
    public function execute(array $exportDetails, OrderInterface $order): bool
    {
        $apiUrl = $this->config->getApiUrl(ScopeInterface::SCOPE_STORE, $order->getStoreId());
        $apiToken = $this->config->getApiToken(ScopeInterface::SCOPE_STORE, $order->getStoreId());

        // Use GuzzleHttp (http://docs.guzzlephp.org/en/stable/) to send the data to our webservice.
        $client = new Client();
        $options = [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $apiToken,
            ],
            'body' => \json_encode($exportDetails),
        ];

        try {
            $response = $client->post($apiUrl, $options);
            $this->processResponse($response);
        } catch (GuzzleException | LocalizedException $ex) {
            $this->logger->error($ex->getMessage(), [
                'details' => $exportDetails
            ]);

            throw $ex;
        }

        return true;
    }

    /**
     * @throws LocalizedException
     */
    private function processResponse(ResponseInterface $response): void
    {
        $responseBody = (string) $response->getBody();
        try {
            $responseData = \json_decode($responseBody, true);
        } catch (\Exception $e) {
            $responseData = [];
        }

        $success = $responseData['success'] ?? false;
        $errorMsg = __($responseData['error']) ?? __('There was a problem: %1', $responseBody);

        if ($response->getStatusCode() !== 200 || !$success) {
            throw new LocalizedException($errorMsg);
        }
    }
}
