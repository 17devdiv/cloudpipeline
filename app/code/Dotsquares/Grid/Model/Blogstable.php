<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Dotsquares\Grid\Model;

use Dotsquares\Grid\Api\Data\BlogstableInterface;
use Magento\Framework\Model\AbstractModel;

class Blogstable extends AbstractModel implements BlogstableInterface
{

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Dotsquares\Grid\Model\ResourceModel\Blogstable::class);
    }

    /**
     * @inheritDoc
     */
    public function getBlogstableId()
    {
        return $this->getData(self::BLOGSTABLE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setBlogstableId($blogstableId)
    {
        return $this->setData(self::BLOGSTABLE_ID, $blogstableId);
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * @inheritDoc
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    /**
     * @inheritDoc
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * @inheritDoc
     */
    public function getContent()
    {
        return $this->getData(self::CONTENT);
    }

    /**
     * @inheritDoc
     */
    public function setContent($content)
    {
        return $this->setData(self::CONTENT, $content);
    }
}

