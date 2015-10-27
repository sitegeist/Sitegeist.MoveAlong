<?php
namespace Sitegeist\MoveAlong\Aspects;

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
use TYPO3\Neos\Routing\Exception\InvalidRequestPathException;
use TYPO3\Neos\Routing\Exception\NoSuchDimensionValueException;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;
use TYPO3\Neos\Routing\FrontendNodeRoutePartHandler;
use TYPO3\Neos\Routing\Exception\NoSuchNodeException;
use TYPO3\Neos\Routing\Exception;
use Sitegeist\MoveAlong\Domain\Service\RuleService;

/**
 * @Flow\Scope("singleton")
 * @Flow\Aspect
 */
class FrontendNodeRoutePartHandlerAspect
{

    protected $isEnabled = TRUE;

    /**
     * @Flow\Inject
     * @var RuleService
     */
    protected $rulesService;

    /**
     * @Flow\Around("method(TYPO3\Neos\Routing\FrontendNodeRoutePartHandler->convertRequestPathToNode())")
     *
     * @param \TYPO3\FLOW\AOP\JoinPointInterface $joinPoint the join point
     *
     * @return mixed
     */
    public function aroundConvertRequestPathToNodeAspect($joinPoint)
    {
        if ($this->isEnabled) {
            $requestPath = $joinPoint->getMethodArgument('requestPath');
            $applyRule = FALSE;

            try {
                $original = $joinPoint->getAdviceChain()->proceed($joinPoint);
            } catch (InvalidRequestPathException $e) {
                $applyRule = TRUE;
            } catch (NoSuchDimensionValueException $e) {
                $applyRule = TRUE;
            } catch (NoSuchNodeException $e) {
                $applyRule = TRUE;
            }

            if ($applyRule) {
                $rule = $this->rulesService->findMatchingRule($requestPath);

                if ($rule !== NULL) {
                    $joinPoint->setMethodArgument('requestPath', $rule['target']);
                    return $joinPoint->getAdviceChain()->proceed($joinPoint);
                }
            }

            return $original;
        }

        return $joinPoint->getAdviceChain()->proceed($joinPoint);
    }


}
