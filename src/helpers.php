<?php

use \Illuminate\Support\HtmlString;

if (!function_exists('theme_view')) {
    /**
     * 获取当前主题的视图
     * @param $view
     * @param array $data
     * @param array $mergeData
     * @return \Illuminate\Contracts\View\View
     */
    function theme_view($view = null, $data = [], $mergeData = [])
    {
        return app(\Ty666\LaravelTheme\Theme::class)->themeView($view, $data, $mergeData);
    }
}

if (! function_exists('mix_with_theme')) {

    function mix_with_theme($path, $manifestDirectory = '')
    {
        static $manifest;

        if (! starts_with($path, '/')) {
            $path = "/{$path}";
        }

        if ($manifestDirectory && ! starts_with($manifestDirectory, '/')) {
            $manifestDirectory = "/{$manifestDirectory}";
        }

        if (file_exists(public_path($manifestDirectory.'/hot'))) {
            return new HtmlString("//localhost:8080{$path}");
        }

        if (! $manifest) {
            if (! file_exists($manifestPath = public_path($manifestDirectory.'/mix-manifest.json'))) {
                throw new Exception('The Mix manifest does not exist.');
            }

            $manifest = json_decode(file_get_contents($manifestPath), true);
        }

        if (! array_key_exists($path, $manifest)) {
            throw new Exception(
                "Unable to locate Mix file: {$path}. Please check your ".
                'webpack.mix.js output paths and try again.'
            );
        }
        if(app(\Ty666\LaravelTheme\Theme::class)->useTheme()){
            return new HtmlString($manifest[$path]);
        }else{
            return new HtmlString($manifestDirectory.$manifest[$path]);
        }

    }
}