<?php

namespace Ty666\LaravelTheme\Console\Command;

use Illuminate\Console\Command;
use Ty666\LaravelTheme\Theme;

class Link extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    public $signature = 'theme:link';

    /**
     * The console command description.
     *
     * @var string
     */
    public $description = 'Create a symbolic link from "public/{public_theme_path}/{theme_name}" to "{theme_path}/{theme_name}/{assets_folder}"';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $config = $this->laravel->make('config')['laravel-theme'];
        $files = $this->laravel->make('files');
        $allThemeConfig = $this->laravel->make(Theme::class)->getAllThemeConfig();

        if(!file_exists(public_path($config['public_theme_folder']))){
            $files->makeDirectory(public_path($config['public_theme_folder']));
        }

        foreach ($allThemeConfig as $themeConfig) {
            $staticPath = $config['theme_path'] . DIRECTORY_SEPARATOR . $themeConfig['theme_id'] . DIRECTORY_SEPARATOR . $config['asset_folder'];
            if (file_exists($staticPath)) {
                $files->link(
                    $staticPath, public_path($config['public_theme_folder'] . DIRECTORY_SEPARATOR . $themeConfig['theme_id'])
                );
                $this->info('The [public' . DIRECTORY_SEPARATOR . $config['public_theme_folder'] . DIRECTORY_SEPARATOR . $themeConfig['theme_id'] . '] directory has been linked.');
            }
        }
    }

}