<?php

if (! function_exists('view')) {
    /**
     * 获取当前主题的视图
     * @param $view
     * @param array $data
     * @param array $mergeData
     * @return \Illuminate\Contracts\View\View
     */
    function view($view = null, $data = [], $mergeData = [])
    {

        return app(\App\Package\Theme\src\Theme::class)->view($view, $data, $mergeData);
    }
}
