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

        $posts = $scraper->getNodes('//ul[@class="post-list"]/li');

        $data = [];

        foreach($posts as $post) {
            $postLinkElement = $scraper->getNode('./h2/a[@class="post-link"]', $post);
            $dateElement = $scraper->getNode('./span[@class="post-meta"]', $post);
            $excerptElement = $scraper->getNode('./p', $post);

            $data[] = [
                'link' => $postLinkElement->getAttribute('href'),
                'title' => $postLinkElement->nodeValue,
                'date' => $dateElement->nodeValue,
                'excerpt' => $excerptElement->nodeValue
            ];
        }

        var_dump($data);
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
