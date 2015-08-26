<?php namespace Scrape;

class WikiScraper
{
    public function scrapeWikipedia()
    {
        $scrapeCount = 0;
        $found = array();
        $pageQueue = new \SplQueue();

        $scraper = new Scrape('https://en.wikipedia.org');
        $pageQueue->enqueue('/');

        while ($scrapeCount < 100 ) {
            $nextPage = $pageQueue->dequeue();
            try {
                $page = $scraper->load($nextPage);
            } catch(\Exception $e) {
                $nextPage = $pageQueue->dequeue();
                $page = $scraper->load($nextPage);
            }

            $links = $page->getNodes("//a[starts-with(@href, '/wiki/')]");

            foreach ($links as $link) {
                $url =  $link->getAttribute('href');

                if ($this->isIgnoredCategory($url)) {
                    continue;
                }

                if (!isset($found[$url])) {
                    $found[$url] = true;
                    $pageQueue->enqueue($url);
                }
            }

            $title = $page->getNode("//h1")->nodeValue;

            echo $title . " " . $nextPage . PHP_EOL;
            $scrapeCount++;
        }

        echo "done!". PHP_EOL;
    }

    // Ignore these Wikipedia Categories from scraping
    private function isIgnoredCategory($url) {

        $categories = [
            'Wikipedia:',
            'Special:',
            'Category:',
            'Help:',
            'User:',
            'Portal:',
            'Help_talk:',
            'User_talk:',
            'Wikipedia_talk:',
            'Portal_talk:',
            'Talk:',
            'File:',
            'Book:',
            'Template:',
            'Template_talk:'
        ];

        foreach ($categories as $category) {
            if (substr($url, 6, strlen($category)) === $category) {
                return true;
            }
        }
        return false;
    }
}
