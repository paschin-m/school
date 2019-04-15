<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\web;

use Yii;
use yii\base\Action;
use yii\base\ViewNotFoundException;

/**
 * ViewAction represents an action that displays a views according to a user-specified parameter.
 *
 * By default, the views being displayed is specified via the `views` GET parameter.
 * The name of the GET parameter can be customized via [[viewParam]].
 *
 * Users specify a views in the format of `path/to/views`, which translates to the views name
 * `ViewPrefix/path/to/views` where `ViewPrefix` is given by [[viewPrefix]]. The views will then
 * be rendered by the [[\yii\base\Controller::render()|render()]] method of the currently active controller.
 *
 * Note that the user-specified views name must start with a word character and can only contain
 * word characters, forward slashes, dots and dashes.
 *
 * @author Alexander Makarov <sam@rmcreative.ru>
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class ViewAction extends Action
{
    /**
     * @var string the name of the GET parameter that contains the requested views name.
     */
    public $viewParam = 'views';
    /**
     * @var string the name of the default views when [[\yii\web\ViewAction::$viewParam]] GET parameter is not provided
     * by user. Defaults to 'index'. This should be in the format of 'path/to/views', similar to that given in the
     * GET parameter.
     * @see \yii\web\ViewAction::$viewPrefix
     */
    public $defaultView = 'index';
    /**
     * @var string a string to be prefixed to the user-specified views name to form a complete views name.
     * For example, if a user requests for `tutorial/chap1`, the corresponding views name will
     * be `pages/tutorial/chap1`, assuming the prefix is `pages`.
     * The actual views file is determined by [[\yii\base\View::findViewFile()]].
     * @see \yii\base\View::findViewFile()
     */
    public $viewPrefix = 'pages';
    /**
     * @var mixed the name of the layout to be applied to the requested views.
     * This will be assigned to [[\yii\base\Controller::$layout]] before the views is rendered.
     * Defaults to null, meaning the controller's layout will be used.
     * If false, no layout will be applied.
     */
    public $layout;


    /**
     * Runs the action.
     * This method displays the views requested by the user.
     * @throws NotFoundHttpException if the views file cannot be found
     */
    public function run()
    {
        $viewName = $this->resolveViewName();
        $this->controller->actionParams[$this->viewParam] = Yii::$app->request->get($this->viewParam);

        $controllerLayout = null;
        if ($this->layout !== null) {
            $controllerLayout = $this->controller->layout;
            $this->controller->layout = $this->layout;
        }

        try {
            $output = $this->render($viewName);

            if ($controllerLayout) {
                $this->controller->layout = $controllerLayout;
            }
        } catch (ViewNotFoundException $e) {
            if ($controllerLayout) {
                $this->controller->layout = $controllerLayout;
            }

            if (YII_DEBUG) {
                throw new NotFoundHttpException($e->getMessage());
            }

            throw new NotFoundHttpException(
                Yii::t('yii', 'The requested views "{name}" was not found.', ['name' => $viewName])
            );
        }

        return $output;
    }

    /**
     * Renders a views.
     *
     * @param string $viewName views name
     * @return string result of the rendering
     */
    protected function render($viewName)
    {
        return $this->controller->render($viewName);
    }

    /**
     * Resolves the views name currently being requested.
     *
     * @return string the resolved views name
     * @throws NotFoundHttpException if the specified views name is invalid
     */
    protected function resolveViewName()
    {
        $viewName = Yii::$app->request->get($this->viewParam, $this->defaultView);

        if (!is_string($viewName) || !preg_match('~^\w(?:(?!\/\.{0,2}\/)[\w\/\-\.])*$~', $viewName)) {
            if (YII_DEBUG) {
                throw new NotFoundHttpException("The requested views \"$viewName\" must start with a word character, must not contain /../ or /./, can contain only word characters, forward slashes, dots and dashes.");
            }

            throw new NotFoundHttpException(Yii::t('yii', 'The requested views "{name}" was not found.', ['name' => $viewName]));
        }

        return empty($this->viewPrefix) ? $viewName : $this->viewPrefix . '/' . $viewName;
    }
}
