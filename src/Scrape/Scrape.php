<?php namespace Scrape;

/**
 * A basic web scraper class
 * @author Markus Östberg <markusos@kth.se>
 */

use Guzzle\Http\Client;
use \DOMDocument;
use \DOMXPath;

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
     * @param $site string Site to scrape
     */
    public function __construct($site)
    {
        $this->webClient = new Client($site);
    }

    /**
     * Load sub page to site.
     * E.g, '/' loads the site root page
     * @param $page string
     * @return $this
     */
    public function load($page) {

        $response = $this->webClient->get($page)->send();

        $html = $response->getBody(true);

        $this->dom = new DOMDocument;

        // Ignore errors caused by unsupported HTML5 tags
        libxml_use_internal_errors(true);
        $this->dom->loadHTML($html);
        libxml_clear_errors();

        return $this;
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
}