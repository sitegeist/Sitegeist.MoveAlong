<?php
namespace Sitegeist\MoveAlong\Routing;

/*
 * Copyright notice
 *
 * (c) 2015 Wilhelm Behncke <behncke@sitegeist.de>
 * All rights reserved
 *
 * This file is part of the Sitegeist/MoveAlong project under MIT License.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Neos\Routing\FrontendNodeRoutePartHandler;
use Sitegeist\MoveAlong\Domain\Service\RuleService;

/**
 * Handles all routes that are not covered by neos Routing due to missinf defaultSuffix
 */
class NotFoundHandlingFrontendNodeRoutePartHandler extends FrontendNodeRoutePartHandler
{

    /**
     * @Flow\Inject
     * @var RuleService
     */
    protected $rulesService;

    /**
     * Looks up a matching rule for providing a 404 page, falls back to default
     * behavior if none is found
     *
     * @param string $requestPath;
     * @return mixed
     */
    protected function convertRequestPathToNode($requestPath)
    {
        $rule = $this->rulesService->findMatchingRule($requestPath);

        if ($rule !== NULL) {
            return parent::convertRequestPathToNode($rule['target']);
        }

        return parent::convertRequestPathToNode($requestPath);
    }

}
