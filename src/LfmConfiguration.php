<?php

namespace UniSharp\LaravelFilemanager;

class LfmConfiguration
{
    private $injectedConfig = [];

    public function __construct(array $injectedConfig)
    {
        $this->injectedConfig = $injectedConfig;
    }

    public function defaultDisplayMode($lfmFolderCategoryName = null)
    {
        $availableDisplayModes = ['grid', 'list'];
        $configOverall = $this->injectedConfig['startup_view'] ?? null;
        $configForCurrentFolderCategory = $this->injectedConfig[$lfmFolderCategoryName]['startup_view'] ?? null;

        if (in_array($configOverall, $availableDisplayModes)) {
            return $configOverall;
        }

        if (in_array($configForCurrentFolderCategory, $availableDisplayModes)) {
            return $configForCurrentFolderCategory;
        }

        return 'list';
    }
}
