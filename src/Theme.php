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
    public function __construct(Filesystem $files, $config)
    {
        $this->files = $files;
        $this->config = $config;
        $this->setCurrentTheme($config['default_theme']);
    }

    public function setCurrentTheme($theme)
    {
        $this->currentTheme = $theme;
        View::setCurrentTheme($theme);
        View::replaceNamespace($theme, $this->config['theme_path'] . DIRECTORY_SEPARATOR . $theme);
    }

    public function getAllThemeConfig()
    {
        $themePaths = $this->files->directories($this->config['theme_path']);
        $themeConfigs = [];
        foreach ($themePaths as $themePath) {
            $configFile = $themePath . DIRECTORY_SEPARATOR . $this->config['config_file_name'];
            if ($this->files->exists($configFile)) {
                $themeConfigs[] = json_decode($this->files->get($configFile), true);
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

    /*public function getContentTemplate()
    {
        $contentTemplates = $this->getCurrentThemeConfig()['content_template'];
        foreach ($contentTemplates as &$contentTemplate) {
            $contentTemplate['title'] .= "({$contentTemplate['file_name']})";
        }
        unset($contentTemplate);
        return $contentTemplates;
    }*/


    public function themeView($view, $data = [], $mergeData = [])
    {
        View::useTheme();
        return View::make($view, $data, $mergeData);
    }

}