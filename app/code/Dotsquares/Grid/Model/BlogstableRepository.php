<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Dotsquares\Grid\Model;

use Dotsquares\Grid\Api\BlogstableRepositoryInterface;
use Dotsquares\Grid\Api\Data\BlogstableInterface;
use Dotsquares\Grid\Api\Data\BlogstableInterfaceFactory;
use Dotsquares\Grid\Api\Data\BlogstableSearchResultsInterfaceFactory;
use Dotsquares\Grid\Model\ResourceModel\Blogstable as ResourceBlogstable;
use Dotsquares\Grid\Model\ResourceModel\Blogstable\CollectionFactory as BlogstableCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class BlogstableRepository implements BlogstableRepositoryInterface
{

    /**
     * @var BlogstableCollectionFactory
     */
    protected $blogstableCollectionFactory;

    /**
     * @var ResourceBlogstable
     */
    protected $resource;

    /**
     * @var Blogstable
     */
    protected $searchResultsFactory;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var BlogstableInterfaceFactory
     */
    protected $blogstableFactory;


    /**
     * @param ResourceBlogstable $resource
     * @param BlogstableInterfaceFactory $blogstableFactory
     * @param BlogstableCollectionFactory $blogstableCollectionFactory
     * @param BlogstableSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceBlogstable $resource,
        BlogstableInterfaceFactory $blogstableFactory,
        BlogstableCollectionFactory $blogstableCollectionFactory,
        BlogstableSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->blogstableFactory = $blogstableFactory;
        $this->blogstableCollectionFactory = $blogstableCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(BlogstableInterface $blogstable)
    {
        try {
            $this->resource->save($blogstable);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the blogstable: %1',
                $exception->getMessage()
            ));
        }
        return $blogstable;
    }

    /**
     * @inheritDoc
     */
    public function get($blogstableId)
    {
        $blogstable = $this->blogstableFactory->create();
        $this->resource->load($blogstable, $blogstableId);
        if (!$blogstable->getId()) {
            throw new NoSuchEntityException(__('blogstable with id "%1" does not exist.', $blogstableId));
        }
        return $blogstable;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->blogstableCollectionFactory->create();
        
        $this->collectionProcessor->process($criteria, $collection);
        
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        
        $items = [];
        foreach ($collection as $model) {
            $items[] = $model;
        }
        
        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function delete(BlogstableInterface $blogstable)
    {
        try {
            $blogstableModel = $this->blogstableFactory->create();
            $this->resource->load($blogstableModel, $blogstable->getBlogstableId());
            $this->resource->delete($blogstableModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the blogstable: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($blogstableId)
    {
        return $this->delete($this->get($blogstableId));
    }
}

