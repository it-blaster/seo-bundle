<?php

namespace ItBlaster\SeoBundle\Model;

use ItBlaster\SeoBundle\Model\om\BaseSeoParam;

class SeoParam extends BaseSeoParam
{
    public function __toString()
    {
        if ($this->isNew()) {
            return '+';
        }

        return $this->getUrl();
    }
}
