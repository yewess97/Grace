<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\File;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Exception\CommonMarkException;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\MarkdownConverter;
use Throwable;

class DocumentationController extends Controller
{
    /**
     * Read the markdown documentation file and display it as HTML.
     *
     * @param string $fileName
     * @return Application|Factory|View
     * @throws CommonMarkException|FileNotFoundException|Throwable
     */
    final public function show(string $fileName = 'main'): Application|Factory|View
    {
        $markdown_page = basename($fileName, '.md');

        $file_path = resource_path("docs/$markdown_page.md");

        if (!File::exists($file_path)) {
            abort(404);
        }

        $markdown_content = File::get($file_path);

        $markdown_content = str_replace(
            ['../../public/storage/', '../../public/assets/'],
            [asset('storage/').'/', asset('assets/').'/'],
            $markdown_content
        );

        // Set 'html_input' to 'allow' so raw <div>, <img>, and <p> tags in the README render properly
        $config = [
            'html_input'         => 'allow', // Options: 'strip', 'allow', or 'escape'
            'allow_unsafe_links' => false,
            'heading_permalink'  => [
                'html_class'        => 'heading-permalink',
                'id_prefix'         => '', // Leave empty so IDs match introduction, business-problem, etc.
                'fragment_prefix'   => '',
                'insert'            => 'before', // Adds the link anchor before heading text
                'min_heading_level' => 1,
                'max_heading_level' => 6,
                'title'             => 'Permalink',
                'symbol'            => ' ', // Anchor symbol shown on hover
            ],
            'slug_normalizer'   => [
                'max_length' => 255,
            ],
        ];

        $environment = new Environment($config);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new GithubFlavoredMarkdownExtension());
        $environment->addExtension(new HeadingPermalinkExtension());

        $converter = new MarkdownConverter($environment);

        $html_content = $converter->convert($markdown_content)->getContent();

        return showView(USER_DOCUMENTATION_VIEW, compact('html_content'));
    }
}
