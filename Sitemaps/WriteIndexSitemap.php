<?php
/**
 * Created by PhpStorm.
 * User: claudicreanga
 * Date: 17/03/2017
 * Time: 11:57
 */

namespace Inviqa\SitemapsHreflang\Sitemaps;

use Inviqa\SitemapsHreflang\Sitemaps\Paths;
use Inviqa\SitemapsHreflang\Logger\Logger;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class WriteIndexSitemap
 * @package Inviqa\SitemapsHreflang\Sitemaps
 */
class WriteIndexSitemap
{
    /**
     * @var Paths
     */
    public $paths;

    /**
     * @var Filesystem
     */
    public $filesystem;

    /**
     * @var Logger
     */
    public $logger;

    /**
     * WriteIndexSitemap constructor.
     * @param Context $context
     * @param Logger $logger
     * @param \Inviqa\SitemapsHreflang\Sitemaps\Paths $paths
     * @param Filesystem $filesystem
     */
    public function __construct(
        Context $context,
        Logger $logger,
        Paths $paths,
        Filesystem $filesystem
    ) {
        $this->paths = $paths;
        $this->logger = $logger;
        $this->filesystem = $filesystem;
    }

    /**
     * Write the sitemap to a new index file
     *
     * @param string $contents
     * @return void
     */
    public function writeFile(string $contents)
    {
        $writer = $this->filesystem->getDirectoryWrite(DirectoryList::PUB);
        $file = $writer->openFile($this->paths->getConfigurationNewSitemapPath()."/".$this->paths->getConfigurationSitemapName().".xml", 'w');

        try {
            $file->lock();
            try {
                $file->write($contents);
            } catch (\Exception $e) {
                $this->logger->debug($e);
            }
            finally {
                $file->unlock();
            }
        } catch (\Exception $e) {
            $this->logger->debug($e);
        } finally {
            $file->close();
        }
    }
}
