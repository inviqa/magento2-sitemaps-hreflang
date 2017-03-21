<?php
/**
 * Created by PhpStorm.
 * User: claudicreanga
 * Date: 17/03/2017
 * Time: 11:21
 */

namespace Inviqa\SitemapsHreflang\Sitemaps;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class Paths
 * @package Inviqa\SitemapsHreflang\Sitemaps
 */
class Paths
{
    /**
     * Retrieve var path
     *
     * @return string
     */
    protected $directory_list;

    /**
     * Paths constructor.
     * @param DirectoryList $directory_list
     */
    public function __construct(
        DirectoryList $directory_list
    )
    {
        $this->directory_list = $directory_list;
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
}