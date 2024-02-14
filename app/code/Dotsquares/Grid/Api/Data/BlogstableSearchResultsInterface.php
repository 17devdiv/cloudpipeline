<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Dotsquares\Grid\Api\Data;

interface BlogstableSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get blogstable list.
     * @return \Dotsquares\Grid\Api\Data\BlogstableInterface[]
     */
    public function getItems();

    /**
     * Set id list.
     * @param \Dotsquares\Grid\Api\Data\BlogstableInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

