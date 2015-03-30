<?php

namespace ItBlaster\SeoBundle\Model;

use ItBlaster\SeoBundle\Model\om\BaseSeoFile;

class SeoFile extends BaseSeoFile
{
    public function __toString()
    {
        if ($this->isNew()) {
            return '+';
        }

        return $this->getName();
    }
}
