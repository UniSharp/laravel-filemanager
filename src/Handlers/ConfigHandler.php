<?php

namespace Unisharp\Laravelfilemanager\Handlers;

class ConfigHandler
{
    public function userField()
    {
        return \Auth::user()->id;
    }
}
