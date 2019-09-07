<?php

namespace Cyclops1101\PageObjectManager\Sources;

use Cyclops1101\PageObjectManager\Pages\Template;

interface SourceInterface
{
    public function getName();
    public function setConfig(array $config);
    public function fetch(Template $template);
    public function store(Template $template);
    public function getErrorLocation($type, $name);
}
