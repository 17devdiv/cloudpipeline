<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Dotsquares\Grid\Api\Data;

interface BlogstableInterface
{

    const ID = 'id';
    const TITLE = 'title';
    const CONTENT = 'content';
    const BLOGSTABLE_ID = 'blogstable_id';

    /**
     * Get blogstable_id
     * @return string|null
     */
    public function getBlogstableId();

    /**
     * Set blogstable_id
     * @param string $blogstableId
     * @return \Dotsquares\Grid\Blogstable\Api\Data\BlogstableInterface
     */
    public function setBlogstableId($blogstableId);

    /**
     * Get id
     * @return string|null
     */
    public function getId();

    /**
     * Set id
     * @param string $id
     * @return \Dotsquares\Grid\Blogstable\Api\Data\BlogstableInterface
     */
    public function setId($id);

    /**
     * Get title
     * @return string|null
     */
    public function getTitle();

    /**
     * Set title
     * @param string $title
     * @return \Dotsquares\Grid\Blogstable\Api\Data\BlogstableInterface
     */
    public function setTitle($title);

    /**
     * Get content
     * @return string|null
     */
    public function getContent();

    /**
     * Set content
     * @param string $content
     * @return \Dotsquares\Grid\Blogstable\Api\Data\BlogstableInterface
     */
    public function setContent($content);
}

