<?php

namespace GoncziAkos\Podcast;

use Silverorange\Castanet\Castanet\Item as CastanetItem;
use Silverorange\Castanet\Castanet;
use DOMNode;

class Item extends CastanetItem
{
    public const DC_NAMESPACE = 'http://purl.org/dc/elements/1.1/';

    private string $postId = '';

    private string $author = '';

    private string $creator = '';

    private array $categories = [];

    public function build(DOMNode $parent): void
    {
        $item = $parent->ownerDocument->createElement('item');
        $parent->appendChild($item);

        $this->buildTitle($item);
        $this->buildLink($item);
        $this->buildPublishDate($item);
        $this->buildGuid($item);
        $this->buildItunesSubtitle($item);
        $this->buildItunesSummary($item);
        $this->buildItunesImage($item);
        $this->buildItunesAuthor($item);
        $this->buildMediaEnclosure($item);
        $this->buildMediaDuration($item);
        $this->buildCategory($item);
        $this->buildCreator($item);
        $this->buildPostId($item);
        $this->buildDescription($item);
    }

    public function setCategories(array $categories): void
    {
        $this->categories = $categories;
    }

    public function addCategory(string $category): void
    {
        $this->categories[] = $category;
    }

    public function setPostId(string $postId): void
    {
        $this->postId = $postId;
    }

    public function setItunesAuthor(string $author): void
    {
        $this->author = $author;
    }

    public function setCreator(string $creator): void
    {
        $this->creator = $creator;
    }

    protected function buildItunesAuthor(DOMNode $parent): void
    {

        if ($this->author) {
            $document = $parent->ownerDocument;

            $node = $document->createElementNS(
                Castanet::ITUNES_NAMESPACE,
                'author'
            );

            $text = $document->createCDATASection($this->author);

            $node->appendChild($text);
            $parent->appendChild($node);
        }
    }

    protected function buildPostId(DOMNode $parent): void
    {
        if ($this->postId) {
            $document = $parent->ownerDocument;

            $text = $document->createTextNode($this->postId);
            $node = $document->createElement('post-id');

            $node->appendChild($text);
            $parent->appendChild($node);
        }
    }

    protected function buildCreator(DOMNode $parent): void
    {
        if ($this->creator) {
            $document = $parent->ownerDocument;

            $node = $document->createElementNS(
                Item::DC_NAMESPACE,
                'creator'
            );

            $text = $document->createCDATASection($this->creator);

            $node->appendChild($text);
            $parent->appendChild($node);
        }
    }

    protected function buildCategory(DOMNode $parent): void
    {
        foreach ($this->categories as $category) {
            if ($category) {
                $document = $parent->ownerDocument;

                $text = $document->createTextNode($category);
                $node = $document->createElement('category');

                $node->appendChild($text);
                $parent->appendChild($node);
            }
        }
    }

}