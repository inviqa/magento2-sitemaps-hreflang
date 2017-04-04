<?php
/**
 * Created by PhpStorm.
 * User: claudicreanga
 * Date: 17/03/2017
 * Time: 11:18
 */

namespace Inviqa\SitemapsHreflang\Sitemaps;

use Inviqa\SitemapsHreflang\Sitemaps\Paths;
use Inviqa\SitemapsHreflang\Logger\Logger;
use Magento\Backend\Block\Template\Context;

/**
 * Class GetSitemaps
 * @package Inviqa\SitemapsHreflang\Sitemaps
 */
class GetSitemaps
{
    /**
     * @var Paths
     */
    public $paths;

    /**
     * @var array
     */
    public $siteMaps;

    /**
     * @var Logger
     */
    public $logger;


    /**
     * GetSitemaps constructor.
     * @param Context $context
     * @param Logger $logger
     * @param \Inviqa\SitemapsHreflang\Sitemaps\Paths $paths
     */
    public function __construct(
        Context $context,
        Logger $logger,
        Paths $paths
    ) {
        $this->paths = $paths;
        $this->logger = $logger;
        $this->paths->createDirectoriesIfTheyDontExist();
        $this->checkFilesToProcess();
    }

    /**
     * Checks if there are sitemaps in the media directory (starting with sitemap word) and sets it in the $files property
     */
    public function checkFilesToProcess()
    {
        $allFilesInIntegrationFolder = glob($this->paths->getExistingSitemapPath().'/sitemap*.xml');
        $this->setSiteMaps($allFilesInIntegrationFolder);
    }

    /**
     * @return array
     */
    public function getSiteMaps(): array
    {
        return $this->siteMaps;
    }
    /**
     * @param mixed $siteMaps
     */
    public function setSiteMaps($siteMaps)
    {
        $this->siteMaps = $siteMaps;
    }
}
