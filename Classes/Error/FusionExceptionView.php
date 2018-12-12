<?php
namespace Sitegeist\MoveAlong\Error;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\View\ViewInterface;
use Neos\Flow\Mvc\View\AbstractView;
use Neos\Neos\Domain\Service\FusionService;
use Neos\Fusion\Core\Runtime as FusionRuntime;
use Neos\Neos\Domain\Repository\SiteRepository;
use Neos\Neos\Domain\Repository\DomainRepository;
use Neos\Neos\Domain\Service\ContentContextFactory;
use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\Flow\I18n\Locale;
use Neos\Flow\I18n\Service;

class FusionExceptionView extends AbstractView implements ViewInterface
{
    /**
     * @Flow\Inject
     * @var Service
     */
    protected $i18nService;

    /**
     * @var FusionService
     * @Flow\Inject
     */
    protected $fusionService;

    /**
     * @var FusionRuntime
     */
    protected $fusionRuntime;

    /**
     * @var SiteRepository
     * @Flow\Inject
     */
    protected $siteRepository;

    /**
     * @var DomainRepository
     * @Flow\Inject
     */
    protected $domainRepository;

    /**
     * @var ContentContextFactory
     * @Flow\Inject
     */
    protected $contentContextFactory;

    /**
     * @return string
     * @throws \Neos\Flow\Security\Exception
     * @throws \Neos\Neos\Domain\Exception
     */
    public function render()
    {
        $domain = $this->domainRepository->findOneByActiveRequest();

        if ($domain) {
            $site = $domain->getSite();
        } else {
            $site = $this->siteRepository->findDefault();
        }

        $contentContext = $this->contentContextFactory->create(['currentSite' => $site]);
        $currentSiteNode = $contentContext->getCurrentSiteNode();

        $fusionRuntime = $this->getFusionRuntime($currentSiteNode);

        $dimensions = $currentSiteNode->getContext()->getDimensions();
        if (array_key_exists('language', $dimensions) && $dimensions['language'] !== array()) {
            $currentLocale = new Locale($dimensions['language'][0]);
            $this->i18nService->getConfiguration()->setCurrentLocale($currentLocale);
            $this->i18nService->getConfiguration()->setFallbackRule(array('strict' => false, 'order' => array_reverse($dimensions['language'])));
        }

        $fusionRuntime->pushContextArray(array_merge(
            $this->variables,
            [
                'node' => $currentSiteNode,
                'documentNode' => $currentSiteNode,
                'site' => $currentSiteNode,
                'editPreviewMode' => null
            ]
        ));

        try {
            $output = $fusionRuntime->render('error');
            $output = $this->extractBodyFromOutput($output);
        } catch (RuntimeException $exception) {
            throw $exception->getPrevious();
        }
        $fusionRuntime->popContext();

        return $output;
    }

    /**
     * @param string $output
     * @return string The message body without the message head
     */
    protected function extractBodyFromOutput($output)
    {
        if (substr($output, 0, 5) === 'HTTP/') {
            $endOfHeader = strpos($output, "\r\n\r\n");
            if ($endOfHeader !== false) {
                $output = substr($output, $endOfHeader + 4);
            }
        }

        return $output;
    }

    /**
     * @param NodeInterface $currentSiteNode
     * @return \Neos\Fusion\Core\Runtime
     */
    protected function getFusionRuntime(NodeInterface $currentSiteNode)
    {
        if ($this->fusionRuntime === null) {
            $this->fusionRuntime = $this->fusionService->createRuntime($currentSiteNode, $this->controllerContext);

            if (isset($this->options['enableContentCache']) && $this->options['enableContentCache'] !== null) {
                $this->fusionRuntime->setEnableContentCache($this->options['enableContentCache']);
            }
        }
        return $this->fusionRuntime;
    }
}
