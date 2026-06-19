<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Page;

class PageController extends Controller
{
    public function show(string $slug)
    {
        $page = Page::published()->where('slug', $slug)->with('sections')->first();

        if (!$page) {
            $title = ucwords(str_replace(['-', '_'], ' ', $slug));
            $page = new Page([
                'title'   => $title,
                'slug'    => $slug,
                'content' => '<p>This page is coming soon. Please check back later or <a href="/contact">contact us</a> for more information.</p>',
                'status'  => 'published',
            ]);
        }

        return view('web.page', compact('page'));
    }
}
