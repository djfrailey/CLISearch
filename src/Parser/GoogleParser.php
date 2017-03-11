<?php

declare(strict_types=1);

namespace David\Parser;

use \Generator;
use \DOMXPath;
use \DOMDocument;
use \DOMNodeList;

class GoogleParser implements ParserInterface
{
    protected $nextPage = 0;

    public function parse(string $responseBody) : Generator
    {
        $this->parsedResults = [];

        libxml_use_internal_errors(true);
        
        $document = new DOMDocument();
        $document->loadHTML($responseBody);
        
        $xPath = new DOMXpath($document);

        $this->nextPage = $this->extractNextPage($xPath);
        yield from $this->extractAnchorTags($xPath);
    }

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

    private function extractAnchorTags(DOMXPath $xPath) : Generator
    {
        $anchorsToExtract = $xPath->query("//h3[@class='r']/a");

        foreach($anchorsToExtract as $element) {
            $textContent = $element->textContent;
            $href = $element->getAttribute('href');
            $href = str_replace('/url?q=', '', $href);

            yield ['title' => $textContent, 'href' => $href];
        }
    }

    public function getNextPage() : int
    {
        return $this->nextPage;
    }
}