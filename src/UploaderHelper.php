<?php

namespace App;

class UploaderHelper
{

    public function getPublicPath(string $path): string
    {
        return '/public/uploads/' . $path;
    }

}