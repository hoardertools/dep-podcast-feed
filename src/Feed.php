<?php

namespace GoncziAkos\Podcast;

use Castanet_Feed;
use DOMDocument;
use Castanet;
use DOMNode;

class Feed extends Castanet_Feed
{
    /**
     * @var string
     */
    private $updatePeriod = 'hourly';

    /**
     * @var int
     */
    private $updateFrequency = 1;

    /**
     * @var string
     */
    private $explicit = 'clean';

    /**
     * @var string
     */
    private $rawvoiceLocation = '';

    /**
     * @var string
     */
    private $rawvoiceDonateTitle = '';

    /**
     * @var string
     */
    private $rawvoiceDonateLink = '';

    /**
     * @var string
     */
    private $rawvoiceSubscribeFeed = '';

    /**
     * @var array
     */
    private $rawvoiceSubscribers = [];

    /**
     * The iTunes category of this feed
     *
     * @var array
     */
    protected $itunes_category = [];

    private $namespace = [
        "xmlns:content" => 'http://purl.org/rss/1.0/modules/content/',
        "xmlns:wfw" => "http://wellformedweb.org/CommentAPI/",
        "xmlns:dc" => "http://purl.org/dc/elements/1.1/",
        "xmlns:atom" => "http://www.w3.org/2005/Atom",
        "xmlns:sy" => "http://purl.org/rss/1.0/modules/syndication/",
        "xmlns:slash" => "http://purl.org/rss/1.0/modules/slash/",
        "xmlns:itunes" => "http://www.itunes.com/dtds/podcast-1.0.dtd",
        "xmlns:rawvoice" => "http://www.rawvoice.com/rawvoiceRssModule/",
        "xmlns:googleplay" => "http://www.google.com/schemas/play-podcasts/1.0",
        "xmlns:georss" => "http://www.georss.org/georss",
        "xmlns:geo" => "http://www.w3.org/2003/01/geo/wgs84_pos#",
    ];

    public function addItunesCategory(
        string $title,
        array $subcategories = array()
    ) {
        $this->itunes_category[$title] = $subcategories;
    }

    public function __toString()
    {
        $document = new DOMDocument('1.0', 'utf-8');
        $document->formatOutput = true;

        $rss = $document->createElement('rss');
        $rss->setAttribute('version', Castanet::RSS_VERSION);

        foreach ($this->namespace as $qualifiedName => $uri) {
            $rss->setAttributeNS(Castanet::XMLNS_NAMESPACE, $qualifiedName, $uri);
        }

        $document->appendChild($rss);

        $this->build($rss);

        return $document->saveXML();
    }

    protected function buildItunesCategories(DOMNode $parent)
    {
        foreach ($this->itunes_category as $category => $subcategories) {
            $document = $parent->ownerDocument;
            $node = $document->createElementNS(
                Castanet::ITUNES_NAMESPACE,
                'category'
            );
            $node->setAttribute('text', $category);
            $parent->appendChild($node);

            foreach ($subcategories as $subcategory) {
                $child_node = $document->createElementNS(
                    Castanet::ITUNES_NAMESPACE,
                    'category'
                );

                $child_node->setAttribute('text', $subcategory);
                $node->appendChild($child_node);
            }
        }
    }

    public function build(DOMNode $parent)
    {
        $document = $parent->ownerDocument;

        $channel = $document->createElement('channel');
        $parent->appendChild($channel);

        $this->buildTitle($channel);
        $this->buildAtomLink($channel);
        $this->buildLink($channel);
        $this->buildDcUpdate($channel);
        $this->buildDescription($channel);
        $this->buildLanguage($channel);
        $this->buildCopyright($channel);
        $this->buildImage($channel);
        $this->buildManagingEditor($channel);
        $this->buildItunesCategories($channel);
        $this->buildItunesAuthor($channel);
        $this->buildItunesOwner($channel);
        $this->buildItunesImage($channel);
        $this->buildItunesExplicit($channel);
        $this->buildItunesBlock($channel);
        $this->buildRawvoice($channel);
        $this->buildItems($channel);
    }

    public function setImage($url, $width = 0, $height = 0)
    {
        $this->image_url = strval($url);
        $this->image_width = intval($width);
        $this->image_height = intval($height);
    }

    protected function buildImage(DOMNode $parent)
    {
        // The standard RSS image element should be a max of 144x400px
        if ($this->image_url != '') {
            $document = $parent->ownerDocument;

            $image_node = $document->createElement('image');
            $parent->appendChild($image_node);

            $node = $document->createElement('url', $this->image_url);
            $image_node->appendChild($node);

            $title = $document->createTextNode($this->title);
            $node = $document->createElement('title');
            $node->appendChild($title);
            $image_node->appendChild($node);

            $link = $document->createTextNode($this->link);
            $node = $document->createElement('link');
            $node->appendChild($link);
            $image_node->appendChild($node);

            if ($this->image_width) {
                $width = $document->createTextNode($this->image_width);
                $node = $document->createElement('width');
                $node->appendChild($width);
                $image_node->appendChild($node);
            }

            if ($this->image_height) {
                $height = $document->createTextNode($this->image_height);
                $node = $document->createElement('height');
                $node->appendChild($height);
                $image_node->appendChild($node);
            }
        }
    }

    protected function buildDcUpdate(DOMNode $parent)
    {
        $document = $parent->ownerDocument;

        $node = $document->createElementNS(
            $this->namespace['xmlns:sy'],
            'updatePeriod',
            $this->updatePeriod
        );

        $parent->appendChild($node);

        $node = $document->createElementNS(
            $this->namespace['xmlns:sy'],
            'updateFrequency',
            $this->updateFrequency
        );

        $parent->appendChild($node);
    }

    protected function buildRawvoice(DOMNode $parent)
    {
        $document = $parent->ownerDocument;
        if ($this->rawvoiceLocation) {
            $node = $document->createElementNS(
                $this->namespace['xmlns:rawvoice'],
                'location',
                $this->rawvoiceLocation
            );
            $parent->appendChild($node);
        }

        if ($this->rawvoiceDonateTitle && $this->rawvoiceDonateLink) {
            $node = $document->createElementNS(
                $this->namespace['xmlns:rawvoice'],
                'donate',
                $this->rawvoiceDonateTitle
            );
            $node->setAttribute('href', $this->rawvoiceDonateLink);
            $parent->appendChild($node);
        }

        if ($this->rawvoiceSubscribeFeed) {
            $node = $document->createElementNS(
                $this->namespace['xmlns:rawvoice'],
                'subscribe'
            );
            $node->setAttribute('feed', $this->rawvoiceSubscribeFeed);
            foreach ($this->rawvoiceSubscribers as $type => $link) {
                $node->setAttribute($type, $link);
            }
            $parent->appendChild($node);
        }
    }

    /**
     * @param string $rawvoiceSubscribeFeed
     */
    public function setRawvoiceSubscribeFeed(string $rawvoiceSubscribeFeed): void
    {
        $this->rawvoiceSubscribeFeed = $rawvoiceSubscribeFeed;
    }

    public function addRawvoiceSubscriber(string $type, string $link)
    {
        $this->rawvoiceSubscribers[$type] = $link;
    }

    public function setRawvoiceLocation(string $rawvoiceLocation): void
    {
        $this->rawvoiceLocation = $rawvoiceLocation;
    }

    public function setRawvoiceDonate(string $title, string $link): void
    {
        $this->rawvoiceDonateTitle = $title;
        $this->rawvoiceDonateLink = $link;
    }

    public function setRawvoiceSubscribers(array $rawvoiceSubscribers): void
    {
        $this->rawvoiceSubscribers = $rawvoiceSubscribers;
    }


    public function setExplicit(string $explicit): void
    {
        $this->explicit = $explicit;
    }

    public function setUpdatePeriod(string $updatePeriod): void
    {
        $this->updatePeriod = $updatePeriod;
    }

    public function setUpdateFrequency(int $updateFrequency): void
    {
        $this->updateFrequency = $updateFrequency;
    }

}