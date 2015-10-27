<?php
namespace Sitegeist\MoveAlong\Domain\Service;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Utility\PositionalArraySorter;

/**
 * @Flow\Scope("singleton")
 */
class RuleService {

  /**
   * @Flow\InjectConfiguration(path="rules")
   */
  protected $rules = array();

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
