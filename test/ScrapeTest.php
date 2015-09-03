<?php namespace Scrape;

class ScrapeTest extends \PHPUnit_Framework_TestCase
{
    public function testWikiScrape()
    {
        (new WikiCrawler())->getArticles(10);
    }

    public function testBasicScrape() {
        $scraper = new Scrape('http://markusos.github.io/');
        $scraper->load('/');

        $siteTitle = $scraper->getNode('//a[@class="site-title"]');
        echo $siteTitle->nodeValue . PHP_EOL;

        $posts = $scraper->getNodes('//ul[@class="post-list"]/li');
        echo "----------------" . PHP_EOL;

        foreach($posts as $post) {
            $postLinkElement = $scraper->getNode('./h2/a[@class="post-link"]', $post);
            $dateElement = $scraper->getNode('./span[@class="post-meta"]', $post);
            $excerptElement = $scraper->getNode('./p', $post);

            echo $postLinkElement->nodeValue . PHP_EOL;
            echo $postLinkElement->getAttribute('href') . PHP_EOL;
            echo $dateElement->nodeValue . PHP_EOL;
            echo $excerptElement->nodeValue . PHP_EOL;
            echo "----------------" . PHP_EOL;
        }
    }

    /**
     * @expectedException              \RuntimeException
     * @expectedExceptionMessageRegExp /Connection timed out after 10\d+ milliseconds/
     */
    public function testTimeOut() {
        $scraper = new Scrape('http://www.google.com:81', 1);
        $scraper->load('/');
    }
}
