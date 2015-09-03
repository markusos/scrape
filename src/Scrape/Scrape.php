<?php namespace Scrape;

/**
 * A basic web scraper class
 * @author Markus Östberg <markusos@kth.se>
 */

use \DOMXPath;
use \DOMDocument;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;

/**
 * Class Scrape
 * @package Scrape
 */
class Scrape
{
    /**
     * @var Client
     */
    private $webClient;
    /**
     * @var DOMDocument
     */
    private $dom;

    /**
     * Init scraper to scrape $site
     * @param string $site Site to scrape
     * @param int $timeout seconds before request times out. Default 2 seconds.
     */
    public function __construct($site, $timeout = 2)
    {
        $this->webClient = new Client(['base_uri' => $site, 'timeout' => $timeout]);
    }

    /**
     * Load sub page to site.
     * E.g, '/' loads the site root page
     * @param string $page Page to load
     * @return $this
     */
    public function load($page) {

        try {
            $response = $this->webClient->get($page);
        } catch(ConnectException $e) {
            throw new \RuntimeException($e->getHandlerContext()['error']);
        }

        $html = $response->getBody();

        $this->dom = new DOMDocument;

        // Ignore errors caused by unsupported HTML5 tags
        libxml_use_internal_errors(true);
        $this->dom->loadHTML($html);
        libxml_clear_errors();

        return $this;
    }

    /**
     * Get first nodes matching xpath query below parent node in DOM tree
     * @param $xpath string selector to query the DOM
     * @param $parent \DOMNode to use as query root node
     * @return \DOMNode
     */
    public function getNode($xpath, $parent=null) {
        $nodes = $this->getNodes($xpath, $parent);

        if ($nodes->length === 0) {
            throw new \RuntimeException("No matching node found");
        }

        return $nodes[0];
    }

    /**
     * Get all nodes matching xpath query below parent node in DOM tree
     * @param $xpath string selector to query the DOM
     * @param $parent \DOMNode to use as query root node
     * @return \DOMNodeList
     */
    public function getNodes($xpath, $parent=null) {
        $DomXpath = new DOMXPath($this->dom);
        $nodes = $DomXpath->query($xpath, $parent);
        return $nodes;
    }
}