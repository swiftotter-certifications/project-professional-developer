<?php
declare(strict_types = 1);
/**
 * @by SwiftOtter, Inc.
 * @website https://swiftotter.com
 **/

namespace SwiftOtter\MockErpApi\Controller\Index;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Laminas\Http\Request;

class Index implements ActionInterface, HttpPostActionInterface
{
    /**
     * @var JsonFactory
     */
    private $jsonFactory;
    /**
     * @var RequestInterface|Request
     */
    private $request;

    public function __construct(
        JsonFactory $jsonFactory,
        RequestInterface $request
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $data = ['success' => false, 'error' => ''];
        /** @var Json $result */
        $result = $this->jsonFactory->create();

        try {
            $this->doExecute();
            $data['success'] = true;
        } catch (\Exception $e) {
            $data['error'] = $e->getMessage();
            $result->setHttpResponseCode(400);
        }

        $result->setData($data);

        return $result;
    }

    /**
     * @throws \Exception
     */
    private function doExecute(): Index
    {
        if (!($this->request instanceof Request)) {
            throw new \Exception('No headers access');
        }

        $headers = $this->request->getHeaders();
        $authHeader = ($headers) ? $headers->get('Authorization') : false;
        if (!$authHeader || !preg_match('#^Bearer#', $authHeader->getFieldValue())) {
            throw new \Exception('No auth token');
        }

        $data = $this->request->getContent();
        if (!$data) {
            throw new \Exception('No order data');
        }
        $data = \json_decode($data, true);
        if (!isset($data['id']) || !isset($data['shipping']) || !isset($data['items'])) {
            throw new \Exception('Minimum order data missing');
        }

        return $this;
    }
}
