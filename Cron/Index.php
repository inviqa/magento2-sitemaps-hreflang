<?php

/**
 * Created by PhpStorm.
 * User: claudicreanga
 * Date: 17/03/2017
 * Time: 10:17
 */

namespace Inviqa\SitemapsHreflang\Cron;

use Inviqa\SitemapsHreflang\Sitemaps\GetSitemaps;
use Inviqa\SitemapsHreflang\Sitemaps\ProcessSitemaps;
use Inviqa\SitemapsHreflang\Logger\Logger;
use Inviqa\SitemapsHreflang\Sitemaps\WriteIndexSitemap;

/**
 * Class Index
 * @package Inviqa\SitemapsHreflang\Cron
 */
class Index
{
    /**
     * @var Logger
     */
    public $logger;

    /**
     * @var ProcessSitemaps
     */
    public $processSitemaps;

    /**
     * @var GetSitemaps
     */
    public $getSitemaps;

    /**
     * @var WriteIndexSitemap
     */
    public $writeIndexSitemap;

    /**
     * Index constructor.
     * @param Logger $logger
     * @param WriteIndexSitemap $writeIndexSitemap
     * @param ProcessSitemaps $processSitemaps
     * @param GetSitemaps $getSitemaps
     */
    public function __construct(
        Logger $logger,
        WriteIndexSitemap $writeIndexSitemap,
        ProcessSitemaps $processSitemaps,
        GetSitemaps $getSitemaps
    ) {
        $this->getSitemaps = $getSitemaps;
        $this->processSitemaps = $processSitemaps;
        $this->writeIndexSitemap = $writeIndexSitemap;
        $this->logger = $logger;
    }

    /**
     * Method called by the cron job. If there are no sitemaps stop here, otherwise process the sitemaps
     *
     * @return void
     */
    public function execute() {
        $siteMaps = $this->getSitemaps->getSiteMaps();
        if(empty($siteMaps)) {
            return;
        }
        $this->processSitemaps->buildNewSiteMap($siteMaps);
        $contents = $this->processSitemaps->getContents();
        $this->writeIndexSitemap->writeFile($contents);
        $this->logger->info("Sitemap has been generated");
    }
}