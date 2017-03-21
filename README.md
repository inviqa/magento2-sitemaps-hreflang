# Sitemaps Hreflang module generator

## Requirements

* PHP 7
* Magento 2

## Goals

* This module aims to merge multiple sitemaps from different stores on a magento multistore instance.
* If a same product have different skus but same path across stores then we use the hreflang atribute to tell google that it is the same product.

## How it works

* This module expects all sitemaps to be in pub/media/ and start with "sitemap"
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

