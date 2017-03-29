# Scrape

A minimal web scraper using Guzzle and PHP's DOM parser.

You can find a short introduction to webparsing using Scrape [here!](http://markusos.github.io/projects/2015/08/25/how-to-build-a-web-scraper.html)

### Example

```php
<?php

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


```

### License

The MIT License (MIT)

Copyright (c) 2015 Markus Östberg
