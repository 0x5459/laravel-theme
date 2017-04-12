<?php

namespace Ty666\LaravelTheme;

use Illuminate\View\FileViewFinder as BaseFileViewFinder;

class FileViewFinder extends BaseFileViewFinder
{
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

		if ($this->hasHintInformation($name)) {
            return $this->views[$name] = $this->findNamespacedView($name);
        }
		
        if (app(Theme::class)->isUseTheme()) {
            $name = $this->currentTheme . static::HINT_PATH_DELIMITER . $name;
            return $this->views[$name] = $this->findNamespacedView($name);
        }
		
        return $this->views[$name] = $this->findInPaths($name, $this->paths);
    }
}