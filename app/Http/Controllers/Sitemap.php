<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\URL;


class Sitemap extends Controller
{

    private function _getLinks($page)
    {
        $url = "https://api.redtube.com/?data=redtube.Videos.searchVideos&output=json&page=$page";
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                // Set Here Your Requesred Headers
                'Content-Type: application/json',
            ),
        ]);

        $array = json_decode(curl_exec($ch), true);
        $err = curl_error($ch);

        curl_close($ch);


        $data = ['video' => []];

        for ($r = 0; $r <= 19; $r++) {
            $insert =
                [
                    'video_id' => $array['videos'][$r]['video']['video_id'],
                    'title' => $array['videos'][$r]['video']['title'],
                    'thumbs' => $array['videos'][$r]['video']['thumbs'],
                    'tags' => $array['videos'][$r]['video']['tags']

                ];
            array_push($data['video'], $insert);
        }

        return ['data' => $data, 'count' => $array["count"]];
    }

    public function getPages()
    {
        $data = [];

        $pages = $this->_getLinks(1)['count'] / 20;

        for ($i = 1; $i <= $pages; $i++) {

            $insert = [
                'loc' => URL::to('/') . "/page/{$i}",
                'lastmod' => date('Y-m-d'),
            ];
            array_push($data, $insert);
        }

        return $data;
    }

    public function getPage($id)
    {
        $page = $this->_getLinks($id)['data'];
        $pages_data = [];

        for ($r = 0; $r <= 19; $r++) {
            $orignalTitle = $page['video'][$r]['title'];
            $id = $page['video'][$r]['video_id'];
            $title = $page['video'][$r]['title'];
            $title = str_replace(' ', '-', $title);
            $title = preg_replace('/[^A-Za-z0-9\-]/', '', $title);
            $title = str_replace('"', "", $title);
            $title = str_replace("'", "", $title);
            $title = str_replace("?", "", $title);
            $title = str_replace("!", "", $title);

            $insert = [
                'loc' => 'https://tinyheaven.net/video/' . [$id][0] . '/' . $title,
                'priority' => '1.0',
                'lastmod' => date('Y-m-d'),
                'changefreq' => 'daily',
                'thumbs' => $page['video'][$r]['thumbs'],
                'orignalTitle' => $orignalTitle,
                'tags' => $page['video'][$r]['tags']
            ];
            array_push($pages_data, $insert);
        }
        return $pages_data;
    }
}
