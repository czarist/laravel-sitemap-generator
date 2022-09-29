<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Sitemap;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect(url("getSitemap"));
});

Route::get('getSitemap', function () {

    $sitemap = App::make("sitemap");

    $sitemapCall = new Sitemap();
    $registros = $sitemapCall->getPages();

    foreach ($registros as $registro) {
        $sitemap->addSitemap($registro['loc'], $registro['lastmod']);
    }

    $sitemap->store('sitemapindex', 'sitemap');

    return redirect(url('sitemap.xml'));
});

Route::get('/page/{id}', function ($id) {
    $sitemap = App::make("sitemap");

    $sitemapCall = new Sitemap();
    $registros = $sitemapCall->getPage($id);


    if ($id == 1) {
        $sitemap->add("https://tinyheaven.net/", date('Y-m-d'), 1.0,  "daily", [["url" => "https://tinyheaven.net//library/img/logo.png"]]);
        $sitemap->add("https://tinyheaven.net/categories",  date('Y-m-d'), 1.0, "daily", [["url" => "https://tinyheaven.net//library/img/logo.png"]]);
        $sitemap->add("https://tinyheaven.net/pornstars", date('Y-m-d'), 1.0,  "daily", [["url" => "https://tinyheaven.net//library/img/logo.png"]]);
    }

    $sitemap->add("https://tinyheaven.net/page/$id", date('Y-m-d'), 1.0,  "daily", [["url" => "https://tinyheaven.net//library/img/logo.png"]]);

    foreach ($registros as $registro) {
        $thumbs = [];
        $tags = [];
        foreach ($registro['thumbs'] as $thumb) {
            array_push($thumbs, ["url" => $thumb['src']]);
        }

        foreach ($registro['tags'] as $tag) {
            array_push($tags, $tag['tag_name']);
        }

        $description = implode(", ", $tags);

        $sitemap->add($registro['loc'], $registro['lastmod'], $registro['priority'], $registro['changefreq'], $thumbs, $registro['orignalTitle'], null, [['title' => $registro['orignalTitle'], 'description' => $description]]);
    }

    $sitemap->store('xml', "pageSitemap_{$id}");

    return redirect(url("pageSitemap_{$id}.xml"));
});
