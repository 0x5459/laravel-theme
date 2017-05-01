<?php
namespace Ty666\LaravelTheme;

use Illuminate\Routing\Controller;
use Response;

class ScreenshotController extends Controller
{
    public function show($themeId)
    {
        $theme = app(Theme::class);
        $themeConfig = $theme->getThemeConfig($themeId);
        $config = $theme->getConfig();
        // 主题图片
        if(!isset($themeConfig['screenshot_name'])){
            $themeConfig['screenshot_name'] = $config['default_screenshot_name'];
        }

        $screenshotPathInfo = pathinfo($themeConfig['screenshot_name'].'.jpg');
        $screenshotPath = $config['themes_path'] . DIRECTORY_SEPARATOR . $themeId . DIRECTORY_SEPARATOR . $screenshotPathInfo['filename'];

        $extensions = isset($screenshotPathInfo['extension'])?[$screenshotPathInfo['extension']]:['jpg', 'png', 'gif'];
        foreach ($extensions as $value) {
            $imageFileName = $screenshotPath . '.' . $value;
            if (file_exists($imageFileName)) {
                if(!$imageSize = getimagesize($imageFileName)){
                    abort(404);
                }
                $imageType = $imageSize[2];

                return Response::stream(function () use ($imageFileName, $imageType){
                    switch ($imageType)
                    {
                        case 1: $src = imagecreatefromgif($imageFileName); break;
                        case 2: $src = imagecreatefromjpeg($imageFileName);  break;
                        case 3: $src = imagecreatefrompng($imageFileName); break;
                        default: abort(404);  break;
                    }
                    switch ($imageType)
                    {
                        case 1: imagegif($src); break;
                        case 2: imagejpeg($src);  break;
                        case 3: imagepng($src); break;
                    }
                }, 200, ['Content-type'=>$imageSize['mime']]);
            }else{
                abort(404);
            }
        }
    }
}