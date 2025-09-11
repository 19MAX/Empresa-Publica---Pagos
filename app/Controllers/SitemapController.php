<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class SitemapController extends BaseController
{
    public function index()
    {
        $response = service('response');
        $response->setHeader('Content-Type', 'application/xml');

        echo '<?xml version="1.0" encoding="UTF-8"?>';
        ?>
        <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

            <url>
                <loc><?= base_url() ?></loc>
                <lastmod>2025-09-11</lastmod>

                <changefreq>daily</changefreq>
                <priority>1.0</priority>
            </url>

        </urlset>
        <?php
    }
}
