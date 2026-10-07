<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Services\SitemapService;

class SitemapController extends Controller
{
    public function index(): void
    {
        header('Content-Type: application/xml; charset=utf-8');
        echo (new SitemapService())->generate();
        exit;
    }

    public function robots(): void
    {
        header('Content-Type: text/plain; charset=utf-8');
        echo (new SitemapService())->robots();
        exit;
    }
}
