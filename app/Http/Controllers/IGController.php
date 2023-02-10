<?php

namespace App\Http\Controllers;

use Instagram\Api;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Instagram\Utils\MediaDownloadHelper;

class IGController extends Controller
{

    /**
     * Get Media of a account
     */
	public function getMedia() {
        $cachePool  = new FilesystemAdapter('Instagram', 0, __DIR__ . '/../cache');
        $api        = new Api($cachePool);
        $img_names  = [];

        //ADD in .env credential
        $api->login(env('INSTA_USERNAME'), env('INSTA_PASSWORD'));
        $profile        = $api->getProfile('cristiano');
        $downloadDir    = public_path() . '/assets';
        $img_names      = [];

        foreach ($profile->getMedias() as $media) {
            $this->downloadMedia($media->getDisplaySrc(), $downloadDir);
            $fileName = substr(str_replace('/', '-', parse_url($media->getDisplaySrc(), PHP_URL_PATH)), 1);
            array_push($img_names, $fileName);
        }

        return view ('gallery', ['images' => $img_names]);
	}

    /**
     * @param string $url URL of the content to be downloaded
     * @param string $folder Directory where the content will be downloaded (default directory is "assets" folder in the dependency folder)
     *
     * @throws InstagramDownloadException
     */
    public static function downloadMedia(string $url, string $folder): string
    {
        if(!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new InstagramDownloadException('Media url is not valid');
        }

        $fileName   = substr(str_replace('/', '-', parse_url($url, PHP_URL_PATH)), 1);
        $content    = file_get_contents($url);
        file_put_contents($folder . '/' . $fileName, $content);

        return $fileName;
    }
}