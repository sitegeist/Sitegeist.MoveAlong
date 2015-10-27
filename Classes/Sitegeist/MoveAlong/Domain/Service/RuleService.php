<?php
namespace Sitegeist\MoveAlong\Domain\Service;

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
use TYPO3\Flow\Utility\PositionalArraySorter;

/**
 * @Flow\Scope("singleton")
 */
class RuleService
{

  /**
   * The configured rules
   *
   * @Flow\InjectConfiguration(path="rules")
   * @var array
   */
  protected $rules = array();

  /**
   * Internal cache for sorted rules
   *
   * @var array
   */
  protected $sortedRules = array();

  /**
   * Find a matching configured rule for the given request path
   *
   * @param string $requestPath
   * @return array|NULL
   */
  public function findMatchingRule($requestPath)
  {
      $rules = $this->getSortedRules();

      foreach ($rules as $rule) {
          if (preg_match('!' . $rule['pattern'] . '!', $requestPath)) {
              return $rule;
          }
      }

      return NULL;
  }

  /**
   * Sort configured rules lazily with flows positional sorting algorithm
   *
   * @return array
   */
  protected function getSortedRules()
  {
      if (empty($this->sortedRules)) {
        $positionalArraySorter = new PositionalArraySorter($this->rules);
        $this->sortedRules = $positionalArraySorter->toArray();
      }

      return $this->sortedRules;
  }

}
