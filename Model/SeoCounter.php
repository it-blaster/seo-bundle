<?php

namespace ItBlaster\SeoBundle\Model;

use ItBlaster\SeoBundle\Model\om\BaseSeoCounter;

class SeoCounter extends BaseSeoCounter
{
    public function __toString()
    {
        if ($this->isNew()) {
            return '+';
        }

        return $this->getTitle();
    }
}

