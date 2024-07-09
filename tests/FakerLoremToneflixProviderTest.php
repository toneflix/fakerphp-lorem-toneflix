<?php

namespace ToneflixCode\FakerLoremToneflix\Tests;

use PHPUnit\Framework\TestCase;
use ToneflixCode\FakerLoremToneflix\FakerLoremToneflixProvider;

class FakerLoremToneflixProviderTest extends TestCase
{
    public function testImageUrlUses640x680AsTheDefaultSize()
    {
        $this->assertMatchesRegularExpression(
            '#^https://lorem.toneflix.com.ng/images\?w=640&h=480#',
            FakerLoremToneflixProvider::imageUrl()
        );
    }

    public function testImageUrlAcceptsCustomWidthAndHeight()
    {
        $this->assertMatchesRegularExpression(
            '#^https://lorem.toneflix.com.ng/images\?w=800&h=400#',
            FakerLoremToneflixProvider::imageUrl(800, 400)
        );
    }

    public function testImageUrlWithPixelate()
    {
        $this->assertMatchesRegularExpression(
            '#^https://lorem.toneflix.com.ng/images\?w=800&h=400&pixelate=#',
            FakerLoremToneflixProvider::imageUrl(800, 400, null, false, null, false, 5)
        );
    }

    public function testImageUrlWithText()
    {
        $this->assertMatchesRegularExpression(
            '#^https://lorem.toneflix.com.ng/images\?w=800&h=400&text=Hello#',
            FakerLoremToneflixProvider::imageUrl(800, 400, null, false, 'Hello', false)
        );
    }

    public function testImageUrlGrey()
    {
        $this->assertMatchesRegularExpression(
            '#^https://lorem.toneflix.com.ng/images\?w=800&h=400&greyscale=#',
            FakerLoremToneflixProvider::imageUrl(800, 400, null, false, null, true)
        );
    }

    public function testImageUrlWithCategoryAndCustomWidthAndHeight()
    {
        $this->assertMatchesRegularExpression(
            '#^https://lorem.toneflix.com.ng/images/avatar\?w=800&h=400#',
            FakerLoremToneflixProvider::imageUrl(800, 400, 'avatar')
        );
    }

    public function testImageUrlWithGreyAndPixelate()
    {
        $imageUrl = FakerLoremToneflixProvider::imageUrl(
            800,
            400,
            null,
            false,
            null,
            true,
            5
        );

        $this->assertSame('https://lorem.toneflix.com.ng/images?w=800&h=400&greyscale=true&pixelate=5', $imageUrl);
    }

    public function testImageUrlAddsARandomGetParameterByDefault()
    {
        $url = FakerLoremToneflixProvider::imageUrl(800, 400);
        $splitUrl = explode('?', $url);

        $this->assertEquals(count($splitUrl), 2);
        $this->assertMatchesRegularExpression('#random=\d{5}#', $splitUrl[1]);
    }

    public function testImageDownloadWithDefaults()
    {
        $file = FakerLoremToneflixProvider::image(sys_get_temp_dir());
        $this->assertFileExists($file);
        if (function_exists('getimagesize')) {
            list($width, $height, $type) = getimagesize($file);

            $this->assertEquals(640, $width);
            $this->assertEquals(480, $height);
            $this->assertEquals(constant('IMAGETYPE_JPEG'), $type);
        } else {
            $this->assertEquals('jpg', pathinfo($file, PATHINFO_EXTENSION));
        }
        if (file_exists($file)) {
            unlink($file);
        }
    }
}
