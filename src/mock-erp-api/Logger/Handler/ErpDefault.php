<?php
declare(strict_types = 1);

namespace SwiftOtter\MockErpApi\Logger\Handler;

use Magento\Framework\Logger\Handler\Base;
use Monolog\Logger;

class ErpDefault extends Base
{
    /**
     * @var string
     */
    protected $fileName = '/var/log/mock_order_export_api.log';

    /**
     * @var int
     */
    protected $loggerType = Logger::DEBUG;
}
