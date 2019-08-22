<?php
declare(strict_types=1);

namespace PunktDe\Archivist\Service;

/*
 * This file is part of the PunktDe.Archivist package.
 *
 * This package is open source software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\Flow\Annotations as Flow;
use Neos\ContentRepository\Domain\Model\NodeInterface;

/**
 * @Flow\Scope("singleton")
 */
class SortingService
{
    /**
     * @Flow\Inject
     * @var EelEvaluationService
     */
    protected $eelEvaluationService;

    /**
     * @param NodeInterface $nodeToBeSorted
     * @param string $eelOrProperty
     * @param string $nodeTypeFilter
     * @throws \Neos\Eel\Exception
     */
    public function sortChildren(NodeInterface $nodeToBeSorted, string $eelOrProperty, $nodeTypeFilter)
    {
        if ($this->eelEvaluationService->isValidExpression($eelOrProperty)) {
            $eelExpression = $eelOrProperty;
        } else {
            $eelExpression = sprintf('${String.toLowerCase(q(a).property("%s")) < String.toLowerCase(q(b).property("%s"))}', $eelOrProperty, $eelOrProperty);
        }

        $this->moveNodeToCorrectPosition($nodeToBeSorted, $eelExpression, $nodeTypeFilter);
    }

    /**
     * @param NodeInterface $nodeToBeSorted
     * @param string $eelExpression
     * @param $nodeTypeFilter
     * @throws \Neos\Eel\Exception
     */
    protected function moveNodeToCorrectPosition(NodeInterface $nodeToBeSorted, string $eelExpression, $nodeTypeFilter)
    {
        $nodes = $nodeToBeSorted->getParent()->getChildNodes($nodeTypeFilter);

        foreach ($nodes as $node) {
            if ($this->eelEvaluationService->evaluate($eelExpression, ['a' => $nodeToBeSorted, 'b' => $node])) {
                $nodeToBeSorted->moveBefore($node);
                break;
            }
        }
    }
}
