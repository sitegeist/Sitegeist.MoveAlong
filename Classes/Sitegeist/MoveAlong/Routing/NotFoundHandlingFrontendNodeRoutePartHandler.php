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
use TYPO3\Neos\Routing\Exception\NoWorkspaceException;
use TYPO3\Neos\Routing\Exception\NoSiteException;
use TYPO3\Neos\Routing\Exception\NoSuchNodeException;
use TYPO3\Neos\Routing\Exception\NoSiteNodeException;
use TYPO3\Neos\Routing\Exception\InvalidRequestPathException;

                    /**
 * Handles all routes that are not covered by neos Routing due to missinf defaultSuffix
 */
class NotFoundHandlingFrontendNodeRoutePartHandler extends FrontendNodeRoutePartHandler
{
    /**
     * The configured rules
     *
     * @Flow\InjectConfiguration(path="enable")
     * @var array
     */
    protected $enable;

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
        if ($this->enable === TRUE) {
            $rules = $this->rulesService->findMatchingRules($requestPath);
            if (count($rules) > 0) {
                foreach ($rules as $rule) {
                    try {
                        $targetPath = preg_replace('!' . $rule['pattern'] . '!', $rule['target'], $requestPath);
                        return parent::convertRequestPathToNode($targetPath);
                    } catch (NoWorkspaceException $e) {
                        // the default exceptions of convertRequestPathToNode are a ignored here
                    } catch (NoSiteException $e) {
                        // the default exceptions of convertRequestPathToNode are a ignored here
                    } catch (NoSuchNodeException $e) {
                        // the default exceptions of convertRequestPathToNode are a ignored here
                    } catch (NoSiteNodeException $e) {
                        // the default exceptions of convertRequestPathToNode are a ignored here
                    } catch (InvalidRequestPathException $e) {
                        // the default exceptions of convertRequestPathToNode are a ignored here
                    }
                }
            }
        }

        return parent::convertRequestPathToNode($requestPath);
    }

}
