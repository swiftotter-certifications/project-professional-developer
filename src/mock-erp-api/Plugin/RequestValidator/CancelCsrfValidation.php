<?php
declare(strict_types = 1);
/**
 * @by SwiftOtter, Inc.
 * @website https://swiftotter.com
 **/

namespace SwiftOtter\MockErpApi\Plugin\RequestValidator;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Request\CsrfValidator;
use Magento\Framework\App\RequestInterface;

/**
 * @see \Magento\Framework\App\Request\CsrfValidator
 */
class CancelCsrfValidation
{
    /**
     * @see \Magento\Framework\App\Request\CsrfValidator::validate
     */
    public function aroundValidate(
        CsrfValidator $subject,
        callable $proceed,
        RequestInterface $request,
        ActionInterface $action
    ): void {
        if ($request->getModuleName() == 'mock_erp_api') {
            // No CSRF validation
            return;
        }
        $proceed($request, $action);
    }
}
