<?php

namespace Ty666\LaravelTheme;

use Illuminate\View\FileViewFinder as BaseFileViewFinder;

class FileViewFinder extends BaseFileViewFinder
{
    protected $isUseTheme = false;
    protected $currentTheme = '';

    public function setCurrentTheme($themeName)
    {
        $this->currentTheme = $themeName;
    }

    public function useTheme()
    {
        $this->isUseTheme = true;
    }

    public function cancelTheme()
    {
        $this->isUseTheme = false;
    }

    /**
     * Get the fully qualified location of the view.
     *
     * @param  string $name
     * @return string
     */
    public function find($name)
    {
        if (isset($this->views[$name])) {
            return $this->views[$name];
        }

        $name = trim($name);

        if ($this->isUseTheme) {
            $name = $this->currentTheme . static::HINT_PATH_DELIMITER . $name;
            return $this->views[$name] = $this->findNamespacedView($name);
        }

        if ($this->hasHintInformation($name)) {
            return $this->views[$name] = $this->findNamespacedView($name);
        }

        return $this->views[$name] = $this->findInPaths($name, $this->paths);
    }
}