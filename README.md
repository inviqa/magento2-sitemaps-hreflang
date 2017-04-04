# Sitemaps Hreflang module generator

## Requirements

* PHP 7
* Magento 2

## Goals

* This module aims to merge multiple sitemaps from different stores on a magento multistore instance.
* If the same product has a different  sku on each store in which its sold but the same Url then Magento doesn’t have the ability to correctly generate the right Href lang tags in the sitemap.  
  This plugin will resolve this and correctly tell google which  is the correct site for the correct visitor using the Href-Lang markup.
## How it works

* This module expects by default all sitemaps to be in pub/media/ and start with word "sitemap". However this can be changed in configuration (the module will create the path if it doesn't exist).
* Then it will merge the sitemaps found there and create a new file called indexSitemap.xml
* The module runs based on a cron early in the morning

## Example

* If the product exists on 1 site, then show the one site in the XML
```<loc>http://www.example.com/productA/</loc>
    <xhtml:link 
                 rel="alternate"
                 hreflang="en-gb"
                 href="http://www.example.com/UK/productA"
                 />
```

* If the product exists on both sites then they should should show: 
```
<loc>http://www.example.com/productB/</loc>
    <xhtml:link 
                 rel="alternate"
                 hreflang="en-gb"
                 href="http://www.example.com/UK/productB"
                 />
    <xhtml:link 
                 rel="alternate"
                 hreflang="en-us"
                 href="http://www.example.com/US/productB"
                 />
```

