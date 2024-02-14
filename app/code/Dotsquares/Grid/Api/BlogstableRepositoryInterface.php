<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Dotsquares\Grid\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface BlogstableRepositoryInterface
{

    /**
     * Save blogstable
     * @param \Dotsquares\Grid\Api\Data\BlogstableInterface $blogstable
     * @return \Dotsquares\Grid\Api\Data\BlogstableInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Dotsquares\Grid\Api\Data\BlogstableInterface $blogstable
    );

    /**
     * Retrieve blogstable
     * @param string $blogstableId
     * @return \Dotsquares\Grid\Api\Data\BlogstableInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($blogstableId);

    /**
     * Retrieve blogstable matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Dotsquares\Grid\Api\Data\BlogstableSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete blogstable
     * @param \Dotsquares\Grid\Api\Data\BlogstableInterface $blogstable
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Dotsquares\Grid\Api\Data\BlogstableInterface $blogstable
    );

    /**
     * Delete blogstable by ID
     * @param string $blogstableId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($blogstableId);
}

