<?php
namespace Sitegeist\MoveAlong\Error;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\View\ViewInterface;
use Neos\Flow\Mvc\View\AbstractView;

class FusionExceptionView extends AbstractView implements ViewInterface
{
    public function render()
    {
        \Neos\Flow\var_dump($this->variables);
        return "i promise i will render fusion";
    }
}
