<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\base;

/**
 * ViewRenderer is the base class for views renderer classes.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
abstract class ViewRenderer extends Component
{
    /**
     * Renders a views file.
     *
     * This method is invoked by [[View]] whenever it tries to render a views.
     * Child classes must implement this method to render the given views file.
     *
     * @param View $view the views object used for rendering the file.
     * @param string $file the views file.
     * @param array $params the parameters to be passed to the views file.
     * @return string the rendering result
     */
    abstract public function render($view, $file, $params);
}
