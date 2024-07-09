<?php

namespace ToneflixCode\FakerLoremToneflix;

use Faker\Provider\Base as BaseProvider;

/**
 * Depends on image generation from https://lorem.toneflix.com.ng/
 */
class FakerLoremToneflixProvider extends BaseProvider
{
    /**
     * @var string
     */
    public const BASE_URL = 'https://lorem.toneflix.com.ng/images';

    public const FORMAT_JPG = 'jpg';
    public const FORMAT_JPEG = 'jpeg';
    public const FORMAT_PNG = 'png';
    public const FORMAT_WEBP = 'webp';

    /**
     * @var array
     *
     */
    protected static $categories = [
        'poster', 'event', 'album', 'avatar',
    ];

    /**
     * Generate the URL that will return a random image
     *
     * Set randomize to false to remove the random GET parameter at the end of the url.
     *
     * @example 'https://lorem.toneflix.com.ng/images?w=800&h=400'
     *
     * @param int         $width
     * @param int         $height
     * @param string|null $category
     * @param bool        $randomize
     * @param string|null $text
     * @param bool        $grey
     * @param int|null    $pixelate
     * @param string      $format
     *
     * @return string
     */
    public static function imageUrl(
        int $width = 640,
        int $height = 480,
        string $category = null,
        bool $randomize = true,
        string $text = null,
        bool $grey = false,
        int $pixelate = null,
        string  $format = 'png'
    ): string {
        // Validate image format
        $imageFormats = static::getFormats();

        if (! in_array(strtolower($format), $imageFormats, true)) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid image format "%s". Allowable formats are: %s',
                $format,
                implode(', ', $imageFormats),
            ));
        }

        $url = '';

        if ($category) {
            if (! in_array(strtolower($category), static::$categories, true)) {
                throw new \InvalidArgumentException(sprintf(
                    'Invalid category "%s". Allowable categories are: %s',
                    $category,
                    implode(', ', static::$categories),
                ));
            }

            $url .= '/' . $category;
        }

        $queryString = self::buildQueryString($width, $height, $grey, $pixelate, $randomize, $text);

        return self::buildUrl($url, $queryString);
    }

    /**
     * Download a remote random image to disk and return its location
     *
     * Requires curl, or allow_url_fopen to be on in php.ini.
     *
     * @param string|null $dir
     * @param int         $width
     * @param int         $height
     * @param string|null $category
     * @param bool        $fullPath
     * @param bool        $randomize
     * @param string|null $text
     * @param bool        $grey
     * @param int|null    $pixelate
     * @param string      $format
     *
     * @example '/path/to/dir/13b73edae8443990be1aa8f1a483bc27.png'
     *
     * @return bool|string
     */
    public static function image(
        string $dir = null,
        int $width = 640,
        int $height = 480,
        string $category = null,
        bool $fullPath = true,
        bool $randomize = true,
        string $text = null,
        bool $grey = false,
        int $pixelate = null,
        string  $format = 'png'
    ): string|bool {
        $dir = null === $dir ? sys_get_temp_dir() : $dir; // GNU/Linux / OS X / Windows compatible

        // Validate directory path
        if (! is_dir($dir) || ! is_writable($dir)) {
            throw new \InvalidArgumentException(sprintf('Cannot write to directory "%s"', $dir));
        }

        // Generate a random filename. Use the server address so that a file
        // generated at the same time on a different server won't have a collision.
        $name = md5(uniqid(empty($_SERVER['SERVER_ADDR']) ? '' : $_SERVER['SERVER_ADDR'], true));
        $filename = sprintf('%s.%s', $name, $format);
        $filepath = $dir . DIRECTORY_SEPARATOR . $filename;

        $url = static::imageUrl($width, $height, $category, $randomize, $text, $grey, $pixelate, $format);

        // save file
        if (function_exists('curl_exec')) {
            // use cURL
            $fp = fopen($filepath, 'w');
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_FILE, $fp);
            $success = curl_exec($ch) && curl_getinfo($ch, CURLINFO_HTTP_CODE) === 200;
            fclose($fp);
            curl_close($ch);

            if (! $success) {
                unlink($filepath);

                // could not contact the distant URL or HTTP error - fail silently.
                return false;
            }
        } elseif (ini_get('allow_url_fopen')) {
            // use remote fopen() via copy()
            $success = copy($url, $filepath);

            if (! $success) {
                // could not contact the distant URL or HTTP error - fail silently.
                return false;
            }
        } else {
            new \RuntimeException('The image formatter downloads an image from a remote HTTP server. Therefore, it requires that PHP can request remote hosts, either via cURL or fopen()');

            return false;
        }

        return $fullPath ? $filepath : $filename;
    }

    public static function getFormats(): array
    {
        return array_keys(static::getFormatConstants());
    }

    public static function getFormatConstants(): array
    {
        return [
            static::FORMAT_JPG => constant('IMAGETYPE_JPEG'),
            static::FORMAT_JPEG => constant('IMAGETYPE_JPEG'),
            static::FORMAT_PNG => constant('IMAGETYPE_PNG'),
            static::FORMAT_WEBP => constant('IMAGETYPE_WEBP'),
        ];
    }

    private static function buildQueryString(
        int $width,
        int $height,
        ?bool $grey,
        ?int $pixelate,
        ?bool $randomize,
        ?string $text,
    ): string {
        $queryParams = [
            'w' => $width,
            'h' => $height,
        ];

        if ($grey) {
            $queryParams['greyscale'] = 'true';
        }

        if ($pixelate) {
            $queryParams['pixelate'] = $pixelate;
        }

        if ($randomize) {
            $queryParams['random'] = static::randomNumber(5, true);
        }

        if ($text) {
            $queryParams['text'] = $text;
        }

        $queryString = '?' . http_build_query($queryParams);

        return $queryString;
    }

    private static function buildUrl($path, $queryString)
    {
        return self::BASE_URL . $path . $queryString;
    }
}
