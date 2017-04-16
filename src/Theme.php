<?php
namespace Ty666\LaravelTheme;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\View;
use Ty666\LaravelTheme\Exception\ThemeNotFound;

class Theme
{
    protected $config;
    protected $files;
    protected $currentTheme;
    protected $currentThemeConfig = null;
    protected $isUseTheme = false;

    public function isUseTheme()
    {
        return $this->isUseTheme;
    }

    public function useTheme()
    {
        $this->isUseTheme = true;
    }

    public function cancelTheme()
    {
        $this->isUseTheme = false;
    }

    public function __construct(Filesystem $files, $config)
    {
        $this->files = $files;
        $this->config = $config;
        $this->setCurrentTheme($config['default_theme']);
    }

    public function getConfig($key = null)
    {
        if (!is_null($key)) {
            return $this->config[$key];
        }
        return $this->config;
    }

    public function setCurrentTheme($theme)
    {
        $this->currentTheme = $theme;
        View::replaceNamespace($theme, $this->config['theme_path'] . DIRECTORY_SEPARATOR . $theme);
    }

    public function getCurrentTheme()
    {
        return $this->currentTheme;
    }

    public function getAllThemeConfig()
    {
        $themePaths = $this->files->directories($this->config['theme_path']);
        $themeConfigs = [];
        foreach ($themePaths as $themePath) {
            $themeId = basename($themePath);
            try {
                $themeConfigs[] = $this->getThemeConfig($themeId) + ['theme_id' => $themeId];
            } catch (ThemeNotFound $e) {
                continue;
            }

        }
        return $themeConfigs;
    }

    public function getThemeConfig($themeId = null)
    {
        if (is_null($themeId)) {
            $themeId = $this->currentTheme;
        }
        $themePath = $this->config['theme_path'] . DIRECTORY_SEPARATOR . $themeId . DIRECTORY_SEPARATOR;
        $configFile = $themePath . $this->config['config_file_name'];

        if (!$this->files->exists($configFile)) {
            throw new ThemeNotFound($themeId . ' 主题不存在');
        }
        $themeConfig = json_decode($this->files->get($configFile), true);

        // 获取主题截图
        $themeConfig['screenshot'] = null;
        $screenshotPath = $themePath . $this->config['static_folder'] . DIRECTORY_SEPARATOR . $this->config['screenshot_name'];
        foreach (['jpg', 'png'] as $value) {

            if ($this->files->exists($screenshotPath . '.' . $value)) {
                $themeConfig['screenshot'] = app('url')->assetWithTheme($this->config['screenshot_name'] . '.' . $value, null, $themeId);
                break;
            }
        }
        return $themeConfig;
    }

    public function getCurrentThemeConfig()
    {
        if (is_null($this->currentThemeConfig)) {
            $this->currentThemeConfig = $this->getThemeConfig();
        }
        return $this->currentThemeConfig;
    }


    public function themeView($view, $data = [], $mergeData = [])
    {
        $this->useTheme();
        return View::make($view, $data, $mergeData);
    }

}