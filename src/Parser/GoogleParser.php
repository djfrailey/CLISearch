<?php

declare(strict_types=1);

namespace Djfrailey\Parser;

use \Generator;
use \DOMXPath;
use \DOMDocument;
use \DOMNodeList;

class GoogleParser implements ParserInterface
{
    /**
     * The next page offset.
     *
     * @var integer
     */
    private $nextPage;

    /**
     * @inheritDoc
     */
    public function parse(string $responseBody) : Generator
    {
        $this->nextPage = 0;

        libxml_use_internal_errors(true);
        
        $document = new DOMDocument();
        $document->loadHTML($responseBody);
        
        $xPath = new DOMXpath($document);

        $this->nextPage = $this->extractNextPage($xPath);
        yield from $this->extractAnchorTags($xPath);
    }

    /**
     * Extracts the next page offset from the DOM.
     *
     * @param  DOMXPath $xPath
     * @return int
     */
    private function extractNextPage(DOMXPath $xPath) : int
    {
        $nextPageAnchorList = $xPath->query("//a[@class='fl']");
        $nextPageAnchorListLength = $nextPageAnchorList->length;
        $nextPage = 0;

        if ($nextPageAnchorListLength > 0) {
            $nextPageAnchorIndex = $nextPageAnchorListLength - 1;
            $nextPageAnchor = $nextPageAnchorList->item($nextPageAnchorIndex);

            if ($nextPageAnchor) {
                $nextPageAnchorText = strtolower($nextPageAnchor->textContent);
                
                if ($nextPageAnchorText === 'next') {
                    $nextPageHref = $nextPageAnchor->getAttribute('href');
                    
                    parse_str($nextPageHref, $nextPageParams);

                    if (isset($nextPageParams['start']) === true
                        && is_numeric($nextPageParams['start'])) {
                        $nextPage = (int) $nextPageParams['start'];
                    }
                }
            }
        }

        return $nextPage;
    }

    /**
     * Extracts all anchor tags pertaining to search results from the DOM
     *
     * @param  DOMXPath $xPath
     * @return Generator
     */
    private function extractAnchorTags(DOMXPath $xPath) : Generator
    {
        $anchorsToExtract = $xPath->query("//h3[@class='r']/a");

        foreach ($anchorsToExtract as $element) {
            $textContent = $element->textContent;
            $href = $element->getAttribute('href');
            $href = str_replace('/url?q=', '', $href);

            yield ['title' => $textContent, 'href' => $href];
        }
    }

    /**
     * @inheritDoc
     */
    public function getNextPage() : int
    {
        return $this->nextPage;
    }
}
