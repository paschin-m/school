<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\base;

use Yii;
use yii\helpers\FileHelper;
use yii\widgets\Block;
use yii\widgets\ContentDecorator;
use yii\widgets\FragmentCache;

/**
 * View represents a views object in the MVC pattern.
 *
 * View provides a set of methods (e.g. [[render()]]) for rendering purpose.
 *
 * For more details and usage information on View, see the [guide article on views](guide:structure-views).
 *
 * @property string|bool $viewFile The views file currently being rendered. False if no views file is being
 * rendered. This property is read-only.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class View extends Component implements DynamicContentAwareInterface
{
    /**
     * @event Event an event that is triggered by [[beginPage()]].
     */
    const EVENT_BEGIN_PAGE = 'beginPage';
    /**
     * @event Event an event that is triggered by [[endPage()]].
     */
    const EVENT_END_PAGE = 'endPage';
    /**
     * @event ViewEvent an event that is triggered by [[renderFile()]] right before it renders a views file.
     */
    const EVENT_BEFORE_RENDER = 'beforeRender';
    /**
     * @event ViewEvent an event that is triggered by [[renderFile()]] right after it renders a views file.
     */
    const EVENT_AFTER_RENDER = 'afterRender';

    /**
     * @var ViewContextInterface the context under which the [[renderFile()]] method is being invoked.
     */
    public $context;
    /**
     * @var mixed custom parameters that are shared among views templates.
     */
    public $params = [];
    /**
     * @var array a list of available renderers indexed by their corresponding supported file extensions.
     * Each renderer may be a views renderer object or the configuration for creating the renderer object.
     * For example, the following configuration enables both Smarty and Twig views renderers:
     *
     * ```php
     * [
     *     'tpl' => ['class' => 'yii\smarty\ViewRenderer'],
     *     'twig' => ['class' => 'yii\twig\ViewRenderer'],
     * ]
     * ```
     *
     * If no renderer is available for the given views file, the views file will be treated as a normal PHP
     * and rendered via [[renderPhpFile()]].
     */
    public $renderers;
    /**
     * @var string the default views file extension. This will be appended to views file names if they don't have file extensions.
     */
    public $defaultExtension = 'php';
    /**
     * @var Theme|array|string the theme object or the configuration for creating the theme object.
     * If not set, it means theming is not enabled.
     */
    public $theme;
    /**
     * @var array a list of named output blocks. The keys are the block names and the values
     * are the corresponding block content. You can call [[beginBlock()]] and [[endBlock()]]
     * to capture small fragments of a views. They can be later accessed somewhere else
     * through this property.
     */
    public $blocks;
    /**
     * @var array|DynamicContentAwareInterface[] a list of currently active dynamic content class instances.
     * This property is used internally to implement the dynamic content caching feature. Do not modify it directly.
     * @internal
     * @deprecated Since 2.0.14. Do not use this property directly. Use methods [[getDynamicContents()]],
     * [[pushDynamicContent()]], [[popDynamicContent()]] instead.
     */
    public $cacheStack = [];
    /**
     * @var array a list of placeholders for embedding dynamic contents. This property
     * is used internally to implement the content caching feature. Do not modify it directly.
     * @internal
     * @deprecated Since 2.0.14. Do not use this property directly. Use methods [[getDynamicPlaceholders()]],
     * [[setDynamicPlaceholders()]], [[addDynamicPlaceholder()]] instead.
     */
    public $dynamicPlaceholders = [];

    /**
     * @var array the views files currently being rendered. There may be multiple views files being
     * rendered at a moment because one views may be rendered within another.
     */
    private $_viewFiles = [];


    /**
     * Initializes the views component.
     */
    public function init()
    {
        parent::init();
        if (is_array($this->theme)) {
            if (!isset($this->theme['class'])) {
                $this->theme['class'] = 'yii\base\Theme';
            }
            $this->theme = Yii::createObject($this->theme);
        } elseif (is_string($this->theme)) {
            $this->theme = Yii::createObject($this->theme);
        }
    }

    /**
     * Renders a views.
     *
     * The views to be rendered can be specified in one of the following formats:
     *
     * - [path alias](guide:concept-aliases) (e.g. "@app/views/site/index");
     * - absolute path within application (e.g. "//site/index"): the views name starts with double slashes.
     *   The actual views file will be looked for under the [[Application::viewPath|views path]] of the application.
     * - absolute path within current module (e.g. "/site/index"): the views name starts with a single slash.
     *   The actual views file will be looked for under the [[Module::viewPath|views path]] of the [[Controller::module|current module]].
     * - relative views (e.g. "index"): the views name does not start with `@` or `/`. The corresponding views file will be
     *   looked for under the [[ViewContextInterface::getViewPath()|views path]] of the views `$context`.
     *   If `$context` is not given, it will be looked for under the directory containing the views currently
     *   being rendered (i.e., this happens when rendering a views within another views).
     *
     * @param string $view the views name.
     * @param array $params the parameters (name-value pairs) that will be extracted and made available in the views file.
     * @param object $context the context to be assigned to the views and can later be accessed via [[context]]
     * in the views. If the context implements [[ViewContextInterface]], it may also be used to locate
     * the views file corresponding to a relative views name.
     * @return string the rendering result
     * @throws ViewNotFoundException if the views file does not exist.
     * @throws InvalidCallException if the views cannot be resolved.
     * @see renderFile()
     */
    public function render($view, $params = [], $context = null)
    {
        $viewFile = $this->findViewFile($view, $context);
        return $this->renderFile($viewFile, $params, $context);
    }

    /**
     * Finds the views file based on the given views name.
     * @param string $view the views name or the [path alias](guide:concept-aliases) of the views file. Please refer to [[render()]]
     * on how to specify this parameter.
     * @param object $context the context to be assigned to the views and can later be accessed via [[context]]
     * in the views. If the context implements [[ViewContextInterface]], it may also be used to locate
     * the views file corresponding to a relative views name.
     * @return string the views file path. Note that the file may not exist.
     * @throws InvalidCallException if a relative views name is given while there is no active context to
     * determine the corresponding views file.
     */
    protected function findViewFile($view, $context = null)
    {
        if (strncmp($view, '@', 1) === 0) {
            // e.g. "@app/views/main"
            $file = Yii::getAlias($view);
        } elseif (strncmp($view, '//', 2) === 0) {
            // e.g. "//layouts/main"
            $file = Yii::$app->getViewPath() . DIRECTORY_SEPARATOR . ltrim($view, '/');
        } elseif (strncmp($view, '/', 1) === 0) {
            // e.g. "/site/index"
            if (Yii::$app->controller !== null) {
                $file = Yii::$app->controller->module->getViewPath() . DIRECTORY_SEPARATOR . ltrim($view, '/');
            } else {
                throw new InvalidCallException("Unable to locate views file for views '$view': no active controller.");
            }
        } elseif ($context instanceof ViewContextInterface) {
            $file = $context->getViewPath() . DIRECTORY_SEPARATOR . $view;
        } elseif (($currentViewFile = $this->getRequestedViewFile()) !== false) {
            $file = dirname($currentViewFile) . DIRECTORY_SEPARATOR . $view;
        } else {
            throw new InvalidCallException("Unable to resolve views file for views '$view': no active views context.");
        }

        if (pathinfo($file, PATHINFO_EXTENSION) !== '') {
            return $file;
        }
        $path = $file . '.' . $this->defaultExtension;
        if ($this->defaultExtension !== 'php' && !is_file($path)) {
            $path = $file . '.php';
        }

        return $path;
    }

    /**
     * Renders a views file.
     *
     * If [[theme]] is enabled (not null), it will try to render the themed version of the views file as long
     * as it is available.
     *
     * The method will call [[FileHelper::localize()]] to localize the views file.
     *
     * If [[renderers|renderer]] is enabled (not null), the method will use it to render the views file.
     * Otherwise, it will simply include the views file as a normal PHP file, capture its output and
     * return it as a string.
     *
     * @param string $viewFile the views file. This can be either an absolute file path or an alias of it.
     * @param array $params the parameters (name-value pairs) that will be extracted and made available in the views file.
     * @param object $context the context that the views should use for rendering the views. If null,
     * existing [[context]] will be used.
     * @return string the rendering result
     * @throws ViewNotFoundException if the views file does not exist
     */
    public function renderFile($viewFile, $params = [], $context = null)
    {
        $viewFile = $requestedFile = Yii::getAlias($viewFile);

        if ($this->theme !== null) {
            $viewFile = $this->theme->applyTo($viewFile);
        }
        if (is_file($viewFile)) {
            $viewFile = FileHelper::localize($viewFile);
        } else {
            throw new ViewNotFoundException("The views file does not exist: $viewFile");
        }

        $oldContext = $this->context;
        if ($context !== null) {
            $this->context = $context;
        }
        $output = '';
        $this->_viewFiles[] = [
            'resolved' => $viewFile,
            'requested' => $requestedFile
        ];

        if ($this->beforeRender($viewFile, $params)) {
            Yii::debug("Rendering views file: $viewFile", __METHOD__);
            $ext = pathinfo($viewFile, PATHINFO_EXTENSION);
            if (isset($this->renderers[$ext])) {
                if (is_array($this->renderers[$ext]) || is_string($this->renderers[$ext])) {
                    $this->renderers[$ext] = Yii::createObject($this->renderers[$ext]);
                }
                /* @var $renderer ViewRenderer */
                $renderer = $this->renderers[$ext];
                $output = $renderer->render($this, $viewFile, $params);
            } else {
                $output = $this->renderPhpFile($viewFile, $params);
            }
            $this->afterRender($viewFile, $params, $output);
        }

        array_pop($this->_viewFiles);
        $this->context = $oldContext;

        return $output;
    }

    /**
     * @return string|bool the views file currently being rendered. False if no views file is being rendered.
     */
    public function getViewFile()
    {
        return empty($this->_viewFiles) ? false : end($this->_viewFiles)['resolved'];
    }

    /**
     * @return string|bool the requested views currently being rendered. False if no views file is being rendered.
     * @since 2.0.16
     */
    protected function getRequestedViewFile()
    {
        return empty($this->_viewFiles) ? false : end($this->_viewFiles)['requested'];
    }

    /**
     * This method is invoked right before [[renderFile()]] renders a views file.
     * The default implementation will trigger the [[EVENT_BEFORE_RENDER]] event.
     * If you override this method, make sure you call the parent implementation first.
     * @param string $viewFile the views file to be rendered.
     * @param array $params the parameter array passed to the [[render()]] method.
     * @return bool whether to continue rendering the views file.
     */
    public function beforeRender($viewFile, $params)
    {
        $event = new ViewEvent([
            'viewFile' => $viewFile,
            'params' => $params,
        ]);
        $this->trigger(self::EVENT_BEFORE_RENDER, $event);

        return $event->isValid;
    }

    /**
     * This method is invoked right after [[renderFile()]] renders a views file.
     * The default implementation will trigger the [[EVENT_AFTER_RENDER]] event.
     * If you override this method, make sure you call the parent implementation first.
     * @param string $viewFile the views file being rendered.
     * @param array $params the parameter array passed to the [[render()]] method.
     * @param string $output the rendering result of the views file. Updates to this parameter
     * will be passed back and returned by [[renderFile()]].
     */
    public function afterRender($viewFile, $params, &$output)
    {
        if ($this->hasEventHandlers(self::EVENT_AFTER_RENDER)) {
            $event = new ViewEvent([
                'viewFile' => $viewFile,
                'params' => $params,
                'output' => $output,
            ]);
            $this->trigger(self::EVENT_AFTER_RENDER, $event);
            $output = $event->output;
        }
    }

    /**
     * Renders a views file as a PHP script.
     *
     * This method treats the views file as a PHP script and includes the file.
     * It extracts the given parameters and makes them available in the views file.
     * The method captures the output of the included views file and returns it as a string.
     *
     * This method should mainly be called by views renderer or [[renderFile()]].
     *
     * @param string $_file_ the views file.
     * @param array $_params_ the parameters (name-value pairs) that will be extracted and made available in the views file.
     * @return string the rendering result
     * @throws \Exception
     * @throws \Throwable
     */
    public function renderPhpFile($_file_, $_params_ = [])
    {
        $_obInitialLevel_ = ob_get_level();
        ob_start();
        ob_implicit_flush(false);
        extract($_params_, EXTR_OVERWRITE);
        try {
            require $_file_;
            return ob_get_clean();
        } catch (\Exception $e) {
            while (ob_get_level() > $_obInitialLevel_) {
                if (!@ob_end_clean()) {
                    ob_clean();
                }
            }
            throw $e;
        } catch (\Throwable $e) {
            while (ob_get_level() > $_obInitialLevel_) {
                if (!@ob_end_clean()) {
                    ob_clean();
                }
            }
            throw $e;
        }
    }

    /**
     * Renders dynamic content returned by the given PHP statements.
     * This method is mainly used together with content caching (fragment caching and page caching)
     * when some portions of the content (called *dynamic content*) should not be cached.
     * The dynamic content must be returned by some PHP statements.
     * @param string $statements the PHP statements for generating the dynamic content.
     * @return string the placeholder of the dynamic content, or the dynamic content if there is no
     * active content cache currently.
     */
    public function renderDynamic($statements)
    {
        if (!empty($this->cacheStack)) {
            $n = count($this->dynamicPlaceholders);
            $placeholder = "<![CDATA[YII-DYNAMIC-$n]]>";
            $this->addDynamicPlaceholder($placeholder, $statements);

            return $placeholder;
        }

        return $this->evaluateDynamicContent($statements);
    }

    /**
     * {@inheritdoc}
     */
    public function getDynamicPlaceholders()
    {
        return $this->dynamicPlaceholders;
    }

    /**
     * {@inheritdoc}
     */
    public function setDynamicPlaceholders($placeholders)
    {
        $this->dynamicPlaceholders = $placeholders;
    }

    /**
     * {@inheritdoc}
     */
    public function addDynamicPlaceholder($placeholder, $statements)
    {
        foreach ($this->cacheStack as $cache) {
            if ($cache instanceof DynamicContentAwareInterface) {
                $cache->addDynamicPlaceholder($placeholder, $statements);
            } else {
                // TODO: Remove in 2.1
                $cache->dynamicPlaceholders[$placeholder] = $statements;
            }
        }
        $this->dynamicPlaceholders[$placeholder] = $statements;
}

    /**
     * Evaluates the given PHP statements.
     * This method is mainly used internally to implement dynamic content feature.
     * @param string $statements the PHP statements to be evaluated.
     * @return mixed the return value of the PHP statements.
     */
    public function evaluateDynamicContent($statements)
    {
        return eval($statements);
    }

    /**
     * Returns a list of currently active dynamic content class instances.
     * @return DynamicContentAwareInterface[] class instances supporting dynamic contents.
     * @since 2.0.14
     */
    public function getDynamicContents()
    {
        return $this->cacheStack;
    }

    /**
     * Adds a class instance supporting dynamic contents to the end of a list of currently active
     * dynamic content class instances.
     * @param DynamicContentAwareInterface $instance class instance supporting dynamic contents.
     * @since 2.0.14
     */
    public function pushDynamicContent(DynamicContentAwareInterface $instance)
    {
        $this->cacheStack[] = $instance;
    }

    /**
     * Removes a last class instance supporting dynamic contents from a list of currently active
     * dynamic content class instances.
     * @since 2.0.14
     */
    public function popDynamicContent()
    {
        array_pop($this->cacheStack);
    }

    /**
     * Begins recording a block.
     *
     * This method is a shortcut to beginning [[Block]].
     * @param string $id the block ID.
     * @param bool $renderInPlace whether to render the block content in place.
     * Defaults to false, meaning the captured block will not be displayed.
     * @return Block the Block widget instance
     */
    public function beginBlock($id, $renderInPlace = false)
    {
        return Block::begin([
            'id' => $id,
            'renderInPlace' => $renderInPlace,
            'views' => $this,
        ]);
    }

    /**
     * Ends recording a block.
     */
    public function endBlock()
    {
        Block::end();
    }

    /**
     * Begins the rendering of content that is to be decorated by the specified views.
     *
     * This method can be used to implement nested layout. For example, a layout can be embedded
     * in another layout file specified as '@app/views/layouts/base.php' like the following:
     *
     * ```php
     * <?php $this->beginContent('@app/views/layouts/base.php'); ?>
     * //...layout content here...
     * <?php $this->endContent(); ?>
     * ```
     *
     * @param string $viewFile the views file that will be used to decorate the content enclosed by this widget.
     * This can be specified as either the views file path or [path alias](guide:concept-aliases).
     * @param array $params the variables (name => value) to be extracted and made available in the decorative views.
     * @return ContentDecorator the ContentDecorator widget instance
     * @see ContentDecorator
     */
    public function beginContent($viewFile, $params = [])
    {
        return ContentDecorator::begin([
            'viewFile' => $viewFile,
            'params' => $params,
            'views' => $this,
        ]);
    }

    /**
     * Ends the rendering of content.
     */
    public function endContent()
    {
        ContentDecorator::end();
    }

    /**
     * Begins fragment caching.
     *
     * This method will display cached content if it is available.
     * If not, it will start caching and would expect an [[endCache()]]
     * call to end the cache and save the content into cache.
     * A typical usage of fragment caching is as follows,
     *
     * ```php
     * if ($this->beginCache($id)) {
     *     // ...generate content here
     *     $this->endCache();
     * }
     * ```
     *
     * @param string $id a unique ID identifying the fragment to be cached.
     * @param array $properties initial property values for [[FragmentCache]]
     * @return bool whether you should generate the content for caching.
     * False if the cached version is available.
     */
    public function beginCache($id, $properties = [])
    {
        $properties['id'] = $id;
        $properties['views'] = $this;
        /* @var $cache FragmentCache */
        $cache = FragmentCache::begin($properties);
        if ($cache->getCachedContent() !== false) {
            $this->endCache();

            return false;
        }

        return true;
    }

    /**
     * Ends fragment caching.
     */
    public function endCache()
    {
        FragmentCache::end();
    }

    /**
     * Marks the beginning of a page.
     */
    public function beginPage()
    {
        ob_start();
        ob_implicit_flush(false);

        $this->trigger(self::EVENT_BEGIN_PAGE);
    }

    /**
     * Marks the ending of a page.
     */
    public function endPage()
    {
        $this->trigger(self::EVENT_END_PAGE);
        ob_end_flush();
    }
}
