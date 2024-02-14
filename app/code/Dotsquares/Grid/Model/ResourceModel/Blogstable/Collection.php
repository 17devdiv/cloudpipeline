<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Dotsquares\Grid\Model\ResourceModel\Blogstable;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    /**
     * @inheritDoc
     */
    protected $_idFieldName = 'blogstable_id';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            \Dotsquares\Grid\Model\Blogstable::class,
            \Dotsquares\Grid\Model\ResourceModel\Blogstable::class
        );
    }
}

