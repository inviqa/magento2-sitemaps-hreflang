<?php
/**
 * Created by PhpStorm.
 * User: claudicreanga
 * Date: 17/03/2017
 * Time: 11:35
 */

namespace Inviqa\SitemapsHreflang\Logger;

use Magento\Framework\Logger\Handler\Base;
use Monolog\Logger as MonologLogger;

class Handler extends Base
{
    /**
     * Logging level
     * @var int
     */
    protected $loggerType =  MonologLogger::INFO;
    /**
     * File name
     * @var string
     */
    public $fileName = '/var/log/sitemaps_hreflang.log';
}