<?php
namespace Ty666\LaravelTheme;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\View;
use Ty666\LaravelTheme\Exception\ThemeNotFound;
class ThemeManager
{
    protected $config;
    protected $files;
    protected $activeTheme;
    protected $activeThemeConfig = null;
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
        $this->setActiveTheme($config['default_theme']);
    }
    public function getConfig($key = null)
    {
        if (!is_null($key)) {
            return $this->config[$key];
        }
        return $this->config;
    }
    public function setActiveTheme($theme)
    {
        $this->activeTheme = $theme;
        View::replaceNamespace('theme_' . $theme, $this->config['themes_path'] . DIRECTORY_SEPARATOR . $theme . DIRECTORY_SEPARATOR . 'views');
    }
    public function getActiveTheme()
    {
        return $this->activeTheme;
    }
    public function getAllThemeConfig()
    {
        $themePaths = $this->files->directories($this->config['themes_path']);
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
            $themeId = $this->activeTheme;
        }
        $themePath = $this->config['themes_path'] . DIRECTORY_SEPARATOR . $themeId . DIRECTORY_SEPARATOR;
        $configFile = $themePath . $this->config['config_file_name'];
        if (!$this->files->exists($configFile)) {
            throw new ThemeNotFound($themeId . ' 主题不存在');
        }
        $themeConfig = json_decode($this->files->get($configFile), true);
        $themeConfig['screenshot_url'] = route($this->config['screenshot_route']['name'], $themeId);
        return $themeConfig;
    }
    public function getActiveThemeConfig()
    {
        if (is_null($this->activeThemeConfig)) {
            $this->activeThemeConfig = $this->getThemeConfig();
        }
        return $this->activeThemeConfig;
    }
    public function themeView($view, $data = [], $mergeData = [])
    {
        $this->useTheme();
        return View::make($view, $data, $mergeData);
    }
    public function getActiveThemePath()
    {
        return $this->config['themes_path'] . DIRECTORY_SEPARATOR . $this->activeTheme;
    }
}