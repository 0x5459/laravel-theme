<?php

namespace Ty666\LaravelTheme;

use Illuminate\Routing\UrlGenerator as BaseUrlGenerator;

class UrlGenerator extends BaseUrlGenerator
{

    public function asset($path, $secure = null)
    {
        if ($this->isValidUrl($path)) {
            return $path;
        }

        $root = $this->formatRoot($this->formatScheme($secure));

        $theme = app(Theme::class);
        if ($theme->isUseTheme()) {
            return $this->removeIndex($root) . '/' . $theme->getConfig('public_theme_folder') . '/' . $theme->getCurrentTheme() . '/' . trim($path, '/');
        }

        return $this->removeIndex($root) . '/' . trim($path, '/');
    }
}
