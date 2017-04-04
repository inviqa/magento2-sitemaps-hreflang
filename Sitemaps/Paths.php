<?php
/**
 * Created by PhpStorm.
 * User: claudicreanga
 * Date: 17/03/2017
 * Time: 11:21
 */

namespace Inviqa\SitemapsHreflang\Sitemaps;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Paths
 * @package Inviqa\SitemapsHreflang\Sitemaps
 */
class Paths
{

    const EXISTING_SITEMAP_PATH = "inviqa_sitemapshreflang/general/path";

    const NEW_SITEMAP_PATH = "inviqa_sitemapshreflang/general/newpath";

    const SITEMAP_NAME = "inviqa_sitemapshreflang/general/name";

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Retrieve var path
     *
     * @return string
     */
    protected $directory_list;

    /**
     * Paths constructor.
     * @param DirectoryList $directory_list
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        DirectoryList $directory_list,
        ScopeConfigInterface $scopeConfig
    )
    {
        $this->directory_list = $directory_list;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Retrieve media path
     *
     * @return string
     */
    public function getMediaFolderPath()
    {
        return $this->directory_list->getPath('media');
    }

    /**
     * @return string|null
     */
    public function getConfigurationSitemapPath()
    {
        return $this->scopeConfig->getValue(self::EXISTING_SITEMAP_PATH, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string|null
     */
    public function getConfigurationSitemapName()
    {
        return $this->scopeConfig->getValue(self::SITEMAP_NAME, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string|null
     */
    public function getConfigurationNewSitemapPath()
    {
        return $this->scopeConfig->getValue(self::NEW_SITEMAP_PATH, ScopeInterface::SCOPE_STORE);
    }

    public function getExistingSitemapPath()
    {
        if ($this->getConfigurationSitemapPath()){
            return $this->directory_list->getRoot()."/pub/".$this->getConfigurationSitemapPath();
        }

        return $this->getMediaFolderPath();
    }

    public function getNewSitemapPath()
    {
        if ($this->getConfigurationNewSitemapPath()){
            return $this->directory_list->getRoot()."/pub/".$this->getConfigurationNewSitemapPath();
        }

        return $this->getMediaFolderPath();
    }

    /**
     * Check if the core directories exists, if not create them
     *
     * @return void
     */
    public function createDirectoriesIfTheyDontExist()
    {
        $directories = array(
            $this->getNewSitemapPath(),
            $this->getExistingSitemapPath()
        );
        foreach ($directories as $directory){
            if(!file_exists($directory)){
                mkdir($directory);
            }
        }
    }
}
