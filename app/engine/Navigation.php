<?php
/*
  +------------------------------------------------------------------------+
  | PhalconEye CMS                                                         |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013 PhalconEye Team (http://phalconeye.com/)            |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file LICENSE.txt.                             |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconeye.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
  | Author: Ivan Vorontsov <ivan.vorontsov@phalconeye.com>                 |
  +------------------------------------------------------------------------+
*/

namespace Engine;

use Phalcon\DI;
use Phalcon\DiInterface;

/**
 * Navigation.
 *
 * @category  PhalconEye
 * @package   Engine
 * @author    Ivan Vorontsov <ivan.vorontsov@phalconeye.com>
 * @copyright 2013 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */
class Navigation
{
    use DependencyInjection {
        DependencyInjection::__construct as protected __DIConstruct;
    }

    /**
     * Items in navigation.
     *
     * @var array
     */
    protected $_items = [];

    /**
     * Html/Text before item title.
     *
     * @var string
     */
    protected $_itemPrependContent = '';

    /**
     * Html/Text after item title.
     *
     * @var string
     */
    protected $_itemAppendContent = '';

    /**
     * Class of navigation.
     *
     * @var string
     */
    protected $_listClass = 'nav';

    /**
     * Class of dropdown item.
     *
     * @var string
     */
    protected $_dropDownItemClass = "dropdown";

    /**
     * Class of dropdown item title.
     *
     * @var string
     */
    protected $_dropDownItemMenuClass = "dropdown-menu";

    /**
     * Class of dropdown item title.
     *
     * @var string
     */
    protected $_dropDownSubItemMenuClass = "dropdown-submenu";

    /**
     * Class of dropdown item switcher.
     *
     * @var string
     */
    protected $_dropDownItemToggleClass = "dropdown-toggle";

    /**
     * Class of dropdown header item.
     *
     * @var string
     */
    protected $_dropDownItemHeaderClass = "nav-header";

    /**
     * Class of dropdown divider item.
     *
     * @var string
     */
    protected $_dropDownItemDividerClass = "divider";

    /**
     * HTML code for dropdown icon
     *
     * @var string
     */
    protected $_dropDownIcon = '<b class="caret"></b>';

    /**
     * Set true to highlight dropdown top item.
     *
     * @var bool
     */
    protected $_highlightActiveDropDownItem = true;

    /**
     * Currently active item, it can be name or href.
     *
     * @var string
     */
    protected $_activeItem = '';

    /**
     * Tag of the navigation.
     *
     * @var string
     */
    protected $_listTag = 'ul';

    /**
     * Tag of navigation item.
     *
     * @var string
     */
    protected $_listItemTag = 'li';

    /**
     * Navigation constructor.
     *
     * @param DiInterface $di Dependency injection.
     */
    public function __construct($di = null)
    {
        $this->__DIConstruct($di);
        $this->_activeItem = substr($this->getDI()->get('request')->get('_url'), 1);
    }

    /**
     * Set list class.
     *
     * @param string $class Class name.
     *
     * @return $this
     */
    public function setListClass($class)
    {
        $this->_listClass = $class;

        return $this;
    }

    /**
     * Set dropdown item class.
     *
     * @param string $class Class name.
     *
     * @return $this
     */
    public function setDropDownItemClass($class)
    {
        $this->_dropDownItemClass = $class;

        return $this;
    }

    /**
     * Set dropdown menu class.
     *
     * @param string $class Class name.
     *
     * @return $this
     */
    public function setDropDownItemMenuClass($class)
    {
        $this->_dropDownItemMenuClass = $class;

        return $this;
    }

    /**
     * Set dropdown icon html.
     *
     * @param string $html Dropdown html.
     *
     * @return $this
     * */
    public function setDropDownIcon($html)
    {
        $this->_dropDownIcon = $html;

        return $this;
    }

    /**
     * Set true to highlight dropdown top item.
     *
     * @param bool $flag Highlight?
     *
     * @return $this
     * */
    public function setEnabledDropDownHighlight($flag = true)
    {
        $this->_highlightActiveDropDownItem = $flag;

        return $this;
    }

    /**
     * Set before content.
     *
     * @param string $content Item content.
     *
     * @return $this
     */
    public function setItemPrependContent($content)
    {
        $this->_itemPrependContent = $content;

        return $this;
    }

    /**
     * Set after content.
     *
     * @param string $content Item after content.
     *
     * @return $this
     */
    public function setItemAppendContent($content)
    {
        $this->_itemAppendContent = $content;

        return $this;
    }

    /**
     * Set navigation list.
     *
     * @param array $items Navigation items.
     *
     * @return $this
     */
    public function setItems($items = [])
    {
        if (empty($items)) {
            return $this;
        }

        $this->_items = $items;

        return $this;
    }

    /**
     * Set active item. It can be name or href.
     *
     * @param string $itemName Active item name.
     *
     * @return $this
     */
    public function setActiveItem($itemName = '')
    {
        $this->_activeItem = $itemName;

        return $this;
    }

    /**
     * Render navigation.
     *
     * @return string
     */
    public function render()
    {
        $content = '';
        if (empty($this->_items)) {
            return $content;
        }

        // short names
        $lt = $this->_listTag;
        $lc = $this->_listClass;

        $content = "<{$lt} class='{$lc}'>";
        $content .= $this->_renderItems($this->_items);
        $content .= "</{$lt}>";

        return $content;
    }

    /**
     * Render navigation items.
     *
     * @param array $items     Items.
     * @param bool  $isSubMenu Is sub items?
     *
     * @return string
     *
     * @TODO: Refactor this.
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _renderItems($items, $isSubMenu = false)
    {
        $content = '';

        // short names
        $lt = $this->_listTag;
        $lit = $this->_listItemTag;
        $pc = $this->_itemPrependContent;
        $ac = $this->_itemAppendContent;
        $ddic = ($isSubMenu ? $this->_dropDownSubItemMenuClass : $this->_dropDownItemClass);
        $ddmc = ($isSubMenu ? '' : $this->_dropDownIcon);
        $ddimc = $this->_dropDownItemMenuClass;
        $dditc = $this->_dropDownItemToggleClass;
        $ddihc = $this->_dropDownItemHeaderClass;
        $ddidc = $this->_dropDownItemDividerClass;

        foreach ($items as $name => $item) {
            // Dropdown menu item.
            if (isset($item['items']) && !empty($item['items'])) {
                $active = ($name == $this->_activeItem ||
                ($this->_highlightActiveDropDownItem && array_key_exists($this->_activeItem, $item['items'])) ?
                    ' active' :
                    ''
                );
                $linkOnclick = (!empty($item['onclick']) ? 'onclick="' . $item['onclick'] . '"' : '');
                $linkTooltip = (!empty($item['tooltip']) ?
                    'title="' . $item['tooltip'] . '" data-placement="' . $item['tooltip_position'] . '"' : '');

                $content .= "<{$lit} class='{$ddic}{$active}'>";
                $prependHTML = (!empty($item['prepend']) ? $item['prepend'] : '');
                $appendHTML = (!empty($item['append']) ? $item['append'] : '');
                $content .= sprintf(
                    '<a %s %s href="javascript:;" class="%s" data-toggle="dropdown">%s%s%s%s%s%s</a>',
                    $linkOnclick,
                    $linkTooltip,
                    $dditc,
                    $prependHTML,
                    $pc,
                    $this->getDI()->get('trans')->query($item['title']),
                    $ac,
                    $ddmc,
                    $appendHTML
                );

                $content .= "<{$lt} class='{$ddimc}'>";
                foreach ($item['items'] as $key => $subitem) {
                    if (is_numeric($key) && !is_array($subitem)) {
                        if ($subitem == 'divider') {
                            $content .= "<{$lit} class='{$ddidc}'></{$lit}>";
                        } else {
                            $content .= "<{$lit} class='{$ddihc}'>";
                            $content .= $this->getDI()->get('trans')->query($subitem);
                            $content .= "</{$lit}>";
                        }
                    } elseif (is_array($subitem)) {
                        $content .= $this->_renderItems([1 => $subitem], true);
                    } else {
                        $active = ($name == $this->_activeItem || $key == $this->_activeItem ? ' class="active"' : '');
                        $content .= "<{$lit}{$active}>";
                        $link = '#';
                        if (strpos($key, 'http') === false && strpos($key, 'javascript:') === false && $key != '/') {
                            $link = $this->getDI()->get('url')->get($key);
                        }
                        $linkTarget = (!empty($item['target']) ? 'target="' . $item['target'] . '"' : '');
                        $linkOnclick = (!empty($item['onclick']) ? 'onclick="' . $item['onclick'] . '"' : '');
                        $linkTooltip = (!empty($item['tooltip']) ?
                            'title="' . $item['tooltip'] . '" data-placement="' . $item['tooltip_position'] . '"' : '');

                        $content .= sprintf(
                            '<a %s %s %s href="%s">%s%s%s</a>',
                            $linkTooltip,
                            $linkTarget,
                            $linkOnclick,
                            $link,
                            $pc,
                            $this->getDI()->get('trans')->query($subitem),
                            $ac
                        );
                        $content .= "</{$lit}>";
                    }

                }
                $content .= "</{$lt}>";
                $content .= "</{$lit}>";
            } else {
                // Normal item.
                $active = ($name == $this->_activeItem ||
                $item['href'] == $this->_activeItem ||
                $this->getDI()->get('url')->get($item['href']) ==
                $this->getDI()->get('config')->application->baseUri . $this->_activeItem ? ' class="active"' : '');

                $prependHTML = (!empty($item['prepend']) ? $item['prepend'] : '');
                $appendHTML = (!empty($item['append']) ? $item['append'] : '');
                $linkTarget = (!empty($item['target']) ? 'target="' . $item['target'] . '"' : '');
                $linkOnclick = (!empty($item['onclick']) ? 'onclick="' . $item['onclick'] . '"' : '');
                $linkTooltip = (!empty($item['tooltip']) ? 'title="' . $item['tooltip'] . '" data-placement="' .
                    $item['tooltip_position'] . '"' : '');

                if (
                    is_array($item['href']) ||
                    (
                        strpos($item['href'], 'http') === false &&
                        strpos($item['href'], 'javascript:') === false &&
                        $item['href'] != '/'
                    )
                ) {
                    $item['href'] = $this->getDI()->get('url')->get($item['href']);
                }

                $content .= "<{$lit}{$active}>";
                $content .= sprintf(
                    '<a %s %s %s href="%s">%s%s%s%s%s</a>',
                    $linkTooltip,
                    $linkTarget,
                    $linkOnclick,
                    $item['href'],
                    $prependHTML,
                    $pc,
                    $this->getDI()->get('trans')->query($item['title']),
                    $ac,
                    $appendHTML
                );
                $content .= "</{$lit}>";
            }
        }

        return $content;
    }
}