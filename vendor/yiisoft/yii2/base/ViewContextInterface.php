<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\base;

/**
 * ViewContextInterface is the interface that should implemented by classes who want to support relative views names.
 *
 * The method [[getViewPath()]] should be implemented to return the views path that may be prefixed to a relative views name.
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 2.0
 */
interface ViewContextInterface
{
    /**
     * @return string the views path that may be prefixed to a relative views name.
     */
    public function getViewPath();
}
