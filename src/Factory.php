<?php

namespace Ty666\LaravelTheme;

use Illuminate\View\Factory as BaseFactory;

class Factory extends BaseFactory
{
    public function setCurrentTheme($themeName)
    {
        $this->finder->setCurrentTheme($themeName);
    }
    public function useTheme()
    {
        $this->finder->useTheme();
    }
    public function cancelTheme()
    {
        $this->finder->cancelTheme();
    }
}
