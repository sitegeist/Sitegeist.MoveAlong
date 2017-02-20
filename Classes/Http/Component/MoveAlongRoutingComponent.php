<?php

namespace Sitegeist\MoveAlong\Http\Component;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Http\Component\ComponentContext;
use Neos\Flow\Http\Component\ComponentInterface;
use Neos\Flow\Mvc\Routing\Router;
use Neos\Flow\Mvc\Routing\RoutingComponent;
use Neos\Flow\Http\Request;

use Sitegeist\MoveAlong\Domain\Service\RuleService;

class MoveAlongRoutingComponent implements ComponentInterface
{

    /**
     * @Flow\Inject
     * @var Router
     */
    protected $router;

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
     * Check if the current request match a redirect
     *
     * @param ComponentContext $componentContext
     * @return void
     */
    public function handle(ComponentContext $componentContext)
    {
        $routingMatchResults = $componentContext->getParameter(RoutingComponent::class, 'matchResults');
        if ($routingMatchResults !== null) {
            return;
        }

        if ($this->enable === true) {
            $requestPath =  ltrim($componentContext->getHttpRequest()->getUri()->getPath(), '/');
            $rules = $this->rulesService->findMatchingRules($requestPath);
            if (count($rules) > 0) {
                foreach ($rules as $rule) {
                    $targetPath = preg_replace('!' . $rule['pattern'] . '!', $rule['target'], $requestPath);

                    $uri = $componentContext->getHttpRequest()->getUri();
                    $uri->setPath('/' . $targetPath);
                    $uri->setQuery('');
                    $uri->setFragment('');

                    $subRoutingMatchResults = $this->router->route(Request::create($uri));
                    if ($subRoutingMatchResults !== null) {
                        $componentContext->setParameter(
                            RoutingComponent::class,
                            'matchResults',
                            $subRoutingMatchResults
                        );
                        return;
                    }
                }
            }
        }
    }
}
