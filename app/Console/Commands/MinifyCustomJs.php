<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Command\Command as CommandAlias;

class MinifyCustomJs extends Command
{
    // The terminal command name & description
    protected $signature   = 'minify-js';
    protected $description = 'Natively minify custom JS modules without external libraries';

    /**
     * Execute the console command.
     *
     * @return int
     * @throws FileNotFoundException
     */
    final public function handle(): int
    {
        $source_directory = public_path('js');

        if (!File::exists($source_directory)) {
            $this->error("Source directory target not found at: {$source_directory}");

            return CommandAlias::FAILURE;
        }

        $this->info("Starting native PHP minification...");

        // Get all files recursively from the source directory
        $files = File::allFiles($source_directory);

        foreach ($files as $file) {
            if ($file->getExtension() === 'js') {
                $source_code = File::get($file->getRealPath());

                // Natively minify using PHP Regex
                $minified_code = $this->minifyEngine($source_code);

                // Determine relative path to keep 'helpers/' structure intact
                $relative_path    = $file->getRelativePathname();
                $destination_path = $source_directory.DIRECTORY_SEPARATOR.$relative_path;

                // Ensure the sub-subdirectory (like public/js/helpers) exists
                File::ensureDirectoryExists(dirname($destination_path));

                // Save the file
                File::put($destination_path, $minified_code);
                $this->line("Minified: js/{$relative_path} -> public/js/{$relative_path}");
            }
        }

        $this->info("All custom modules minified successfully to public/js/!");

        return CommandAlias::SUCCESS;
    }

    /**
     * Natively minifies JavaScript code using PHP regex.
     *
     * @param string $code
     * @return string
     */
    private function minifyEngine(string $code): string
    {
        // 1. Strip single line comments (keeping URLs safe)
        $code = preg_replace('/(^|[^\/])\/\/.*$/m', '$1', $code);

        // 2. Strip multi-line block comments
        $code = preg_replace('/(\/\*([\s\S]*?)\*\/)/', '', $code);

        // 3. Remove spaces around operators & structural characters
        $code = preg_replace('/\s*([=\+\-\*\/\{\}\(\)\[\]\:\;\,\<\>\!\|\&\?\.])\s*/', '$1', $code);

        // 4. Collapse multiple spaces and newlines into one single space
        $code = preg_replace('/\s+/', ' ', $code);

        return trim($code);
    }
}
