<?php

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

if (!function_exists('asset_with_theme')) {
    /**
     * 使用当前请求的 scheme（HTTP或HTTPS）为指定的主题前端资源生成一个URL：
     */
    function asset_with_theme($path, $secure = null, $themeId = null)
    {
        return app('url')->asset_with_theme($path, $secure, $themeId);
    }
}

if (!function_exists('mix')) {
    /**
     * Get the path to a versioned Mix file.
     *
     * @param  string $path
     * @param  string $manifestDirectory
     * @return \Illuminate\Support\HtmlString
     *
     * @throws \Exception
     */
    function mix($path, $manifestDirectory = '')
    {
        static $manifest;

        if (!starts_with($path, '/')) {
            $path = "/{$path}";
        }

        $theme = app(\Ty666\LaravelTheme\Theme::class);
        if (!$manifestDirectory && $theme->isUseTheme()) {
            $manifestDirectory = '/'.$theme->getConfig('public_themes_folder') . '/' . $theme->getCurrentTheme();
        }else if ($manifestDirectory && !starts_with($manifestDirectory, '/')) {
            $manifestDirectory = "/{$manifestDirectory}";
        }

        if (file_exists(public_path($manifestDirectory . '/hot'))) {
            return new \Illuminate\Support\HtmlString("//localhost:8080{$path}");
        }

        if (!$manifest) {
            if (!file_exists($manifestPath = public_path($manifestDirectory . '/mix-manifest.json'))) {
                throw new Exception('The Mix manifest does not exist.');
            }

            $manifest = json_decode(file_get_contents($manifestPath), true);
        }

        if (!array_key_exists($path, $manifest)) {
            throw new Exception(
                "Unable to locate Mix file: {$path}. Please check your " .
                'webpack.mix.js output paths and try again.'
            );
        }

        return new \Illuminate\Support\HtmlString($manifestDirectory . $manifest[$path]);
    }
}