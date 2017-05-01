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