<?php

/**
 * Created by PhpStorm.
 * User: claudicreanga
 * Date: 17/03/2017
 * Time: 11:16
 */

namespace Inviqa\SitemapsHreflang\Sitemaps;

use Inviqa\SitemapsHreflang\Logger\Logger;

/**
 * Class ProcessSitemaps
 * @package Inviqa\SitemapsHreflang\Sitemaps
 */
class ProcessSitemaps
{

    /**
     * @var string
     */
    public $contents = "";

    /**
     * @var Logger
     */
    public $logger;

    /**
     * @var string
     */
    public $hrefLangFormatSingle = "";

    /**
     * @var string
     */
    public $hrefLangFormatMultiple = "";

    /**
     * ProcessSitemaps constructor.
     * @param Logger $logger
     */
    public function __construct(
        Logger $logger
    )
    {
        $this->logger = $logger;
        $this->setHrefLangFormatSingle(
            '<url><loc>%s</loc>
            <xhtml:link 
            rel="alternate"
            hreflang="%s"
            href="%s"
            />'
        );
        $this->setHrefLangFormatMultiple(
            '<xhtml:link 
            rel="alternate"
            hreflang="%s"
            href="%s"
            />'
        );

    }

    /**
     * Merges all arrays into a big one and calls the hreflang parser
     * @param array $siteMaps
     * @return void
     */
    public function buildNewSiteMap(array $siteMaps)
    {
        $allUrls = [];
        $this->setContents(
            '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">'
        );
        foreach ($siteMaps as $siteMap){
            $this->logger->info('Start parsing the sitemap '.$siteMap);
            $allUrls = array_merge($allUrls, $this->getUrlsFromSitemaps($siteMap));
        }
        $this->parseHrefLang($allUrls);
        $this->setContents("</urlset>");
    }

    /**
     * Looks for duplicate paths down the line and puts them together
     *
     * @param array $urls
     * @return void
     */
    public function parseHrefLang(array $urls)
    {
        $keysDuplicateUrls = [];
        $size = count($urls);
        for ($i = 0; $i < $size; $i++){
            if(in_array($i, $keysDuplicateUrls)){
                continue;
            }
            $path = $urls[$i]["path"];
            $otherStoresHaveSameUrl = [];
            for($j = $i+1; $j < $size; $j++){
                if(in_array($j, $keysDuplicateUrls)){
                    continue;
                }
                if ($path == $urls[$j]["path"]){
                    $otherStoresHaveSameUrl[] = $urls[$i]["store"];
                    $otherStoresHaveSameUrl[] = $urls[$j]["store"];
                    $keysDuplicateUrls[] = $j;
                }
            }
            $fullUrl = $urls[$i]["fullUrl"] ?? "";
            $hostAndScheme = $urls[$i]["host"] ?? ""; // needed so that we can rebuild the duplicate urls with the right store code
            $lastMod = $urls[$i]["lastmod"] ?? null;
            $changeFreq = $urls[$i]["changefreq"] ?? null;
            $priority = $urls[$i]["priority"] ?? null;
            $images = $urls[$i]["images"] ?? null;
            if ($otherStoresHaveSameUrl){
                $this->setContents("<url><loc>$fullUrl</loc>");
                foreach($otherStoresHaveSameUrl as $store){
                    $this->setContents(sprintf($this->getHrefLangFormatMultiple(), $this->getHrefLangFromStoreCode($store), $hostAndScheme."/".$store."/".$path ));
                }
            } else {
                $this->setContents(sprintf($this->getHrefLangFormatSingle(), $fullUrl, $this->getHrefLangFromStoreCode($urls[$i]["store"]), $fullUrl));
            }
            if($lastMod){
                $this->setContents("<lastmod>$lastMod</lastmod>");
            }
            if($changeFreq){
                $this->setContents("<changefreq>$changeFreq</changefreq>");
            }
            if($priority){
                $this->setContents("<priority>$priority</priority>");
            }
            if($images){
                foreach ($images as $image){
                    $this->setContents("<image:image>");
                    foreach($image as $key => $value){
                        if($key == "image:title"){
                            $value = str_replace('&', ' &amp;', $value);
                        }
                        $this->setContents("<".$key.">".$value."</".$key.">");
                    }
                    $this->setContents("</image:image>");
                }
            }
            $this->setContents("</url>");
        }

    }

    /**
     * Gets the urls from a sitemap and returns an array containing
     * store, path, lastmod, changefreq and priority
     *
     * @param string $sitemap
     * @return array
     */
    public function getUrlsFromSitemaps(string $sitemap): array
    {
        $urls = [];
        $DomDocument = new \DOMDocument(); // todo use DI
        $DomDocument->preserveWhiteSpace = false;
        $DomDocument->load($sitemap);
        $DomNodeList = $DomDocument->getElementsByTagName('url');

        foreach($DomNodeList as $url) {
            $item = [];
            $images = [];
            foreach ( $url->childNodes as $childNode ) {
                if ($childNode->nodeName == 'loc') {

                    $urlPath = parse_url($childNode->nodeValue, PHP_URL_PATH); // get url path
                    preg_match('/^\/([^\/]*)./', $urlPath, $matches); // find the store code
                    $urlPathWithoutCode = str_replace($matches[0], "", $urlPath); // get the path without store code
                    $hostAndScheme = str_replace($urlPath, "", $childNode->nodeValue);
                    $item = array_merge($item, ["store" => $matches[1], "path" => $urlPathWithoutCode, "fullUrl" => $childNode->nodeValue, "host" => $hostAndScheme]);

                }
                if ($childNode->nodeName == 'lastmod') {
                    $item = array_merge($item, ["lastmod" => $childNode->nodeValue]);
                }
                if ($childNode->nodeName == 'changefreq') {
                    $item = array_merge($item, ["changefreq" => $childNode->nodeValue]);
                }
                if ($childNode->nodeName == 'priority') {
                    $item = array_merge($item, ["priority" => $childNode->nodeValue]);
                }
                if ($childNode->nodeName == 'image:image') {
                    $imageContainer = [];
                    foreach($childNode->childNodes as $imageNode){
                        $imageContainer[$imageNode->nodeName] = $imageNode->nodeValue;
                    }
                    $images[] = $imageContainer;
                }

            }
            $item = array_merge($item, ["images" => $images]);
            $urls[] = $item;
        }

        return $urls;
    }

    /**
     * @param string $code
     * @return string
     */
    public function getHrefLangFromStoreCode(string $code): string
    {
        switch ($code){
            case "uk":
                return "en-gb";
            case "us":
                return "en-us";
            case "au":
                return "en-au";
            case "ie":
                return "en-ie";
            case "ca":
                return "en-ca";
            case "fr":
                return "fr-fr";
            case "nl":
                return "nl-nl";
            case "pt":
                return "pt-pt";
            case "es":
                return "es-es";
            case "mx":
                return "es-mx";
            case "de":
                return "de-de";
            case "ro":
                return "ro-ro";
            case "it":
                return "it-it";
            default:
                return "en-gb";
        }
    }

    /**
     * @return string
     */
    public function getContents(): string
    {
        return $this->contents;
    }

    /**
     * @param string $contents
     */
    public function setContents(string $contents)
    {
        $this->contents .= $contents;
    }

    /**
     * @return string
     */
    public function getHrefLangFormatSingle(): string
    {
        return $this->hrefLangFormatSingle;
    }

    /**
     * @param string $hrefLangFormatSingle
     */
    public function setHrefLangFormatSingle(string $hrefLangFormatSingle)
    {
        $this->hrefLangFormatSingle = $hrefLangFormatSingle;
    }

    /**
     * @return string
     */
    public function getHrefLangFormatMultiple(): string
    {
        return $this->hrefLangFormatMultiple;
    }

    /**
     * @param string $hrefLangFormatMultiple
     */
    public function setHrefLangFormatMultiple(string $hrefLangFormatMultiple)
    {
        $this->hrefLangFormatMultiple = $hrefLangFormatMultiple;
    }

}