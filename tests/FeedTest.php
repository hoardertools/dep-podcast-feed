<?php

namespace GoncziAkos\Podcast\Tests;

use GoncziAkos\Podcast\Feed;
use GoncziAkos\Podcast\Item;
use PHPUnit\Framework\TestCase;
use DateTime;

class FeedTest extends TestCase
{

    public function testMakeFeedAndItems(): void
    {
        $feed = new Feed(
            "Channel Title",
            "http://Page Url",
            "Channel Description"
        );
        $feed->setLanguage("hu-HU");
        $feed->setCopyright('All rights reserved');
        $feed->setUpdatePeriod('hourly');
        $feed->setUpdateFrequency(1);
        $feed->setImage("http://Itunes Image Url"); // min 1400 x 1400 pixel
        $feed->setItunesExplicit(true);
        $feed->setItunesOwner('Channel Owner');
        $feed->setItunesOwnerEmail('Channel Email');
        $feed->setItunesAuthor('Channel Author');
        $feed->setItunesImage("http://Itunes Image Url");
        $feed->addItunesCategory("News", ["Politics"]);
        $feed->setAtomLink("http://Feed Url");
        $feed->setRawvoiceDonate('DonateTitle', 'http://DonateLink');
        $feed->setRawvoiceLocation('Budapest, Hungary');
        $feed->setRawvoiceSubscribeFeed("http://Feed Url");
        $feed->setRawvoiceSubscribers([
            'html' => "http://Subscriber Html Page Url",
            'spotify' => "http://Spotify Subscriber Url"
        ]);

        $posts = [
            [
                'title' => 'title',
                'link' => 'link',
                'description' => 'description',
                'publishDate' => '2020-02-23 22:48:00',
                'mediaUrl' => 'http:// Mp3 Media Url',
                'mediaSize' => 12312323, // in byte
                'mediaDuration' => 123123, // in sec
                'mediaMimeType' => 'audio/mpeg',
                'subTitle' => 'subTitle',
                'summary' => 'summary',
                'imageUrl' => 'http:// Image Url',
                'author' => 'author',
                'creator' => 'creator',
                'tags' => ['tag 1', 'tag 2'],
            ]
        ];

        foreach ($posts as $post) {
            $feedItem = new Item();
            $feedItem->setTitle($post['title']);
            $feedItem->setLink($post['link']);
            $feedItem->setGuid($post['link']);
            $feedItem->setDescription($post['description']);
            $feedItem->setPublishDate(new DateTime($post['publishDate']));
            $feedItem->setMediaUrl($post['mediaUrl']);
            $feedItem->setMediaSize($post['mediaSize']);
            $feedItem->setMediaDuration($post['mediaDuration']);
            $feedItem->setMediaMimeType($post['mediaMimeType']);
            $feedItem->setItunesSubtitle($post['subTitle']);
            $feedItem->setItunesSummary($post['summary']);
            $feedItem->setItunesImage($post['imageUrl']); // min 1400 x 1400 pixel
            $feedItem->setItunesAuthor($post['author']);
            $feedItem->setCreator($post['creator']);
            $feedItem->setCategories($post['tags']); // array( "tag 1", "tag 2")
            $feed->addItem($feedItem);
        }

        $expected = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<rss xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:wfw="http://wellformedweb.org/CommentAPI/" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:sy="http://purl.org/rss/1.0/modules/syndication/" xmlns:slash="http://purl.org/rss/1.0/modules/slash/" xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd" xmlns:rawvoice="http://www.rawvoice.com/rawvoiceRssModule/" xmlns:googleplay="http://www.google.com/schemas/play-podcasts/1.0" xmlns:georss="http://www.georss.org/georss" xmlns:geo="http://www.w3.org/2003/01/geo/wgs84_pos#" version="2.0">
  <channel>
    <title>Channel Title</title>
    <atom:link href="http://Feed Url" rel="self" type="application/rss+xml"/>
    <link>http://Page Url</link>
    <sy:updatePeriod>hourly</sy:updatePeriod>
    <sy:updateFrequency>1</sy:updateFrequency>
    <description><![CDATA[Channel Description]]></description>
    <itunes:summary><![CDATA[Channel Description]]></itunes:summary>
    <language>hu-HU</language>
    <copyright>All rights reserved</copyright>
    <image>
      <url>http://Itunes Image Url</url>
      <title>Channel Title</title>
      <link>http://Page Url</link>
    </image>
    <itunes:category text="News">
      <itunes:category text="Politics"/>
    </itunes:category>
    <itunes:author>Channel Author</itunes:author>
    <itunes:owner>
      <itunes:email>Channel Email</itunes:email>
      <itunes:name>Channel Owner</itunes:name>
    </itunes:owner>
    <itunes:image href="http://Itunes Image Url"/>
    <itunes:explicit>yes</itunes:explicit>
    <itunes:block>no</itunes:block>
    <rawvoice:location>Budapest, Hungary</rawvoice:location>
    <rawvoice:donate href="http://DonateLink">DonateTitle</rawvoice:donate>
    <rawvoice:subscribe feed="http://Feed Url" html="http://Subscriber Html Page Url" spotify="http://Spotify Subscriber Url"/>
    <item>
      <title>title</title>
      <link>link</link>
      <pubDate>Sun, 23 Feb 2020 22:48:00 +0000</pubDate>
      <guid>link</guid>
      <itunes:subtitle><![CDATA[subTitle]]></itunes:subtitle>
      <itunes:summary><![CDATA[summary]]></itunes:summary>
      <itunes:image href="http:// Image Url"/>
      <itunes:author><![CDATA[author]]></itunes:author>
      <enclosure url="http:// Mp3 Media Url" length="12312323" type="audio/mpeg"/>
      <itunes:duration>34:12:03</itunes:duration>
      <category>tag 1</category>
      <category>tag 2</category>
      <dc:creator><![CDATA[creator]]></dc:creator>
      <description><![CDATA[description]]></description>
    </item>
  </channel>
</rss>

XML;;

        $this->assertEquals($expected, (string)$feed);
    }
}