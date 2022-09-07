<?php
declare(strict_types=1);
/**
 * @by SwiftOtter, Inc. 12/31/19
 * @website https://swiftotter.com
 **/

namespace SwiftOtter\OrderExport\Action;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Magento\Framework\Exception\LocalizedException;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use SwiftOtter\OrderExport\Model\Config;

class PushDetailsToWebservice
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var Config
     */
    private $config;

    public function __construct(
        LoggerInterface $logger,
        Config $config
    ) {
        $this->logger = $logger;
        $this->config = $config;
    }

    /**
     * @throws GuzzleException
     * @throws LocalizedException
     */
    public function execute(array $orderDetails): bool
    {
        $apiUrl = $this->config->getApiUrl();
        $apiToken = $this->config->getApiToken();
        if (!$apiUrl || !$apiToken) {
            throw new LocalizedException(__('API connection information is not configured'));
        }

        try {
            // Use GuzzleHttp (http://docs.guzzlephp.org/en/stable/) to send the data to our webservice.

            $client = new Client();
            $options = [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $apiToken,
                ],
                'body' => \json_encode($orderDetails),
            ];

            $response = $client->post($apiUrl, $options);
            $this->processResponse($response);
        } catch (GuzzleException | LocalizedException $ex) {
            $this->logger->error($ex->getMessage(), [
                'details' => $orderDetails
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
        } catch (\Throwable $ex) {
            $responseData = [];
        }

        $success = $responseData['success'] ?? false;
        $errorMsg = __($responseData['error']) ?? __('There was a problem: %1', $responseBody);

        if ($response->getStatusCode() !== 200 || !$success) {
            throw new LocalizedException($errorMsg);
        }
    }
}
