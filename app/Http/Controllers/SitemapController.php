<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\JobListing;
use App\Models\Page;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        // Homepage
        $urls = [
            [
                'loc' => url('/'),
                'lastmod' => now(),
            ]
        ];

        // CMS Pages (your /{slug})
        $pages = Page::query()
            ->where('is_published', true)
            ->get(['slug', 'updated_at'])
            ->map(function ($page) {
                return [
                    'loc' => url('/' . ltrim($page->slug, '/')),
                    'lastmod' => $page->updated_at ?? now(),
                ];
            })
            ->toArray();

        // Blog posts (if they are also /{slug})
        $posts = BlogPost::query()
            ->where('is_published', true)
            ->get(['slug', 'updated_at'])
            ->map(function ($post) {
                return [
                    'loc' => url('/' . ltrim($post->slug, '/')),
                    'lastmod' => $post->updated_at ?? now(),
                ];
            })
            ->toArray();

        // Job listings (if they are also /{slug})
        $jobs = JobListing::query()
            ->where('is_active', true)
            ->get(['slug', 'updated_at'])
            ->map(function ($job) {
                return [
                    'loc' => url('/' . ltrim($job->slug, '/')),
                    'lastmod' => $job->updated_at ?? now(),
                ];
            })
            ->toArray();

        $urls = array_merge($urls, $pages, $posts, $jobs);

        return response()
            ->view('sitemap.xml', compact('urls'))
            ->header('Content-Type', 'application/xml');
    }
}