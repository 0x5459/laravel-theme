<?php
namespace Ty666\LaravelTheme;

use Ty666\LaravelTheme\Exception\ThemeNotFound;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\View;

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
        if(!is_null($key)){
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
            $configFile = $themePath . DIRECTORY_SEPARATOR . $this->config['config_file_name'];
            if ($this->files->exists($configFile)) {
                $themeConfigs[basename($themePath)] = json_decode($this->files->get($configFile), true);
            }
        }
        return $themeConfigs;
    }

    public function getThemeConfig($themeName = null)
    {
        if (is_null($themeName)) {
            $themeName = $this->currentTheme;
        }
        $configFile = $this->config['theme_path']. DIRECTORY_SEPARATOR . $themeName . DIRECTORY_SEPARATOR . $this->config['config_file_name'];

        if (!$this->files->exists($configFile)) {
            throw new ThemeNotFound($themeName.' 主题不存在');
        }
        return json_decode($this->files->get($configFile), true);
    }

    public function getCurrentThemeConfig()
    {
        if(is_null($this->currentThemeConfig)){
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