<?php
// Jivoo
// Copyright (c) 2015 Niels Sonnich Poulsen (http://nielssp.dk)
// Licensed under the MIT license.
// See the LICENSE file or http://opensource.org/licenses/MIT for more information.
namespace Jivoo\View\Compile;

use Jivoo\View\InvalidTemplateException;

/**
 * Implements the default template macros.
 */
class DefaultMacros extends Macros
{

    /**
     * Replaces the node with PHP code.
     * @param HtmlNode $node Node.
     * @param TemplateNode|null $value Macro parameter.
     */
    public function outerhtmlMacro(HtmlNode $node, TemplateNode $value)
    {
        $node->replaceWith($value);
    }

    /**
     * Replaces the content of the node with PHP code.
     * @param HtmlNode $node Node.
     * @param TemplateNode|null $value Macro parameter.
     */
    public function innerhtmlMacro(HtmlNode $node, TemplateNode $value)
    {
        $node->clear()->append($value);
    }

    /**
     * Replaces the node with PHP code (with html entities replaced).
     * @param HtmlNode $node Node.
     * @param TemplateNode|null $value Macro parameter.
     */
    public function outertextMacro(HtmlNode $node, TemplateNode $value)
    {
        if ($value instanceof PhpNode) {
            $value = new PhpNode('\Jivoo\View\Html::h(' . $value->code . ')');
        }
        $node->replaceWith($value);
    }

    /**
     * Replaces the content of the node with PHP code (with html entities replaced).
     * @param HtmlNode $node Node.
     * @param TemplateNode|null $value Macro parameter.
     */
    public function innertextMacro(HtmlNode $node, TemplateNode $value)
    {
        if ($value instanceof PhpNode) {
            $value = new PhpNode('\Jivoo\View\Html::h(' . $value->code . ')');
        }
        $node->clear()->append($value);
    }

    /**
     * Replaces the content of the node with PHP code.
     * @param HtmlNode $node Node.
     * @param TemplateNode|null $value Macro parameter.
     */
    public function htmlMacro(HtmlNode $node, TemplateNode $value)
    {
        $this->innerhtmlMacro($node, $value);
    }

    /**
     * Replaces the content of the node with PHP code (with html entities replaced).
     * @param HtmlNode $node Node.
     * @param TemplateNode|null $value Macro parameter.
     */
    public function textMacro(HtmlNode $node, TemplateNode $value)
    {
        $this->innertextMacro($node, $value);
    }

    /**
     * Sets the primary (root) node.
     * @param HtmlNode $node Node.
     * @param TemplateNode|null $value Macro parameter.
     */
    public function mainMacro(HtmlNode $node, TemplateNode $value = null)
    {
        
    }

    /**
     * Import a styleheet or script. If the current node is a '<link />' it is
     * removed.
     * @param HtmlNode $node Node.
     * @param TemplateNode|null $value Macro parameter.
     */
    public function importMacro(HtmlNode $node, TemplateNode $value)
    {
        $node->root->prepend(new PhpNode('$this->import(' . PhpNode::expr($value)->code . ')', true));
        if ($node->tag == 'link') {
            $node->detach();
        }
    }

    /**
     * Replaces node with list of script and/or stylesheet imports.
     * @param HtmlNode $node Node.
     * @param TemplateNode|null $value Macro parameter.
     */
    public function importsMacro(HtmlNode $node, TemplateNode $value = null)
    {
        if (isset($value)) {
            $node->root->prepend(new PhpNode('$this->import(' . PhpNode::expr($value)->code . ')', true));
        }
        if ($node->tag == 'link') {
            $node->replaceWith(new PhpNode('$this->resourceBlock()'));
        }
    }

    /**
     * Replaces the content of the node with another template.
     * @param HtmlNode $node Node.
     * @param TemplateNode|null $value Macro parameter.
     */
    public function embedMacro(HtmlNode $node, TemplateNode $value)
    {
        $node->replaceWith(new PhpNode('$this->embed(' . PhpNode::expr($value)->code . ')'));
    }

    /**
     * Replaces the content of the node with the content of a block.
     * @param HtmlNode $node Node.
     * @param TemplateNode|null $value Macro parameter.
     */
    public function blockMacro(HtmlNode $node, TemplateNode $value)
    {
        $node->replaceWith(new PhpNode('$this->block(' . PhpNode::expr($value)->code . ')'));
    }
    
    /**
     * Assign the node to a block.
     * @param HtmlNode $node Node.
     * @param TemplateNode|null $value Macro parameter.
     */
    public function assignMacro(HtmlNode $node, TemplateNode $value)
    {
        $node->before(new PhpNode('$this->begin(' . PhpNode::expr($value)->code . ');'));
        $node->after(new PhpNode('$this->end();'));
    }
    
    /**
     * Append the node to a block.
     * @param HtmlNode $node Node.
     * @param TemplateNode|null $value Macro parameter.
     */
    public function appendMacro(HtmlNode $node, TemplateNode $value)
    {
        $node->before(new PhpNode('$this->append(' . PhpNode::expr($value)->code . ');'));
        $node->after(new PhpNode('$this->end();'));
    }
    
    /**
     * Prepend the node to a block.
     * @param HtmlNode $node Node.
     * @param TemplateNode|null $value Macro parameter.
     */
    public function prependMacro(HtmlNode $node, TemplateNode $value)
    {
        $node->before(new PhpNode('$this->prepend(' . PhpNode::expr($value)->code . ');'));
        $node->after(new PhpNode('$this->end();'));
    }

    /**
     * Sets the layout.
     * @param HtmlNode $node Node.
     * @param TemplateNode|null $value Macro parameter.
     */
    public function layoutMacro(HtmlNode $node, TemplateNode $value)
    {
        $node->before(new PhpNode('$this->layout(' . PhpNode::expr($value)->code . ')', true));
    }

    /**
     * Disables the layout.
     * @param HtmlNode $node Node.
     * @param TemplateNode|null $value Macro parameter.
     */
    public function nolayoutMacro(HtmlNode $node, TemplateNode $value = null)
    {
        $node->before(new PhpNode('$this->disableLayout()', true));
    }

    /**
     * Sets the parent template.
     * @param HtmlNode $node Node.
     * @param TemplateNode $value Macro parameter.
     */
    public function extendMacro(HtmlNode $node, TemplateNode $value)
    {
        $node->before(new PhpNode('$this->extend(' . PhpNode::expr($value)->code . ')', true));
    }

    /**
     * Removes the node from the DOM.
     * @param HtmlNode $node Node.
     * @param TemplateNode|null $value Macro parameter.
     */
    public function ignoreMacro(HtmlNode $node, TemplateNode $value = null)
    {
        $node->detach();
    }

    /**
     * Begins or continues (if parameter omitted) an if block around the node.
     * @param HtmlNode $node Node.
     * @param TemplateNode $value Macro parameter.
     */
    public function ifMacro(HtmlNode $node, TemplateNode $value)
    {
        if (!isset($value)) {
            $prev = $node->prev;
            $between = array();
            do {
                if ($prev instanceof IfNode) {
                    \Jivoo\Assume::that(count($prev->else) == 0);
                    $between = array_reverse($between);
                    foreach ($between as $betweenNode) {
                        $betweenNode->detach();
                        $prev->then->append($betweenNode);
                    }
                    $node->detach();
                    $prev->then->append($node);
                    return;
                }
                $between[] = $prev;
                $prev = $prev->prev;
            } while (isset($prev));
            throw new InvalidTemplateException('Empty if-node must follow another if-node.');
        }
        $ifNode = new IfNode(PhpNode::expr($value)->code);
        $node->replaceWith($ifNode);
        $ifNode->then->append($node);
    }

    /**
     * Begins or continues (if parameter omitted) an else block around the node.
     * @param HtmlNode $node Node.
     * @param TemplateNode|null $value Macro parameter.
     */
    public function elseMacro(HtmlNode $node, TemplateNode $value = null)
    {
        $prev = $node->prev;
        $between = array();
        do {
            if ($prev instanceof IfNode) {
                $between = array_reverse($between);
                foreach ($between as $betweenNode) {
                    $betweenNode->detach();
                    $prev->then->append($betweenNode);
                }
                $node->detach();
                $prev->else->append($node);
                return;
            }
            $between[] = $prev;
            $prev = $prev->prev;
        } while (isset($prev));
        throw new InvalidTemplateException('Else-node must follow an if-node or another else-node.');
    }

    /**
     * Begins or continues (if parameter omitted) a foreach block around the node.
     * @param HtmlNode $node Node.
     * @param TemplateNode|null $value Macro parameter.
     */
    public function foreachMacro(HtmlNode $node, TemplateNode $value)
    {
        if (!isset($value)) {
            if ($node->prev instanceof ForeachNode) {
                $foreachNode = $node->prev;
                $node->detach();
                $foreachNode->append($node);
                return;
            }
            throw new InvalidTemplateException('Empty foreach-node must folow another foreach-node');
        }
        $foreachNode = new ForeachNode(PhpNode::expr($value)->code);
        $node->replaceWith($foreachNode);
        $foreachNode->append($node);
    }

    /**
     * Sets the datetime-attribute to the specified UNIX timestamp.
     * @param HtmlNode $node Node.
     * @param TemplateNode|null $value Macro parameter.
     */
    public function datetimeMacro(HtmlNode $node, TemplateNode $value)
    {
        $node->setAttribute('datetime', new PhpNode('date(\'c\', ' . PhpNode::expr($value)->code . ')'));
    }

    /**
     * Sets the href-attribute to the specified route-value (see {@see \Jivoo\Routing\Routing}).
     * @param HtmlNode $node Node.
     * @param TemplateNode|null $value Macro parameter.
     */
    public function hrefMacro(HtmlNode $node, TemplateNode $value)
    {
        if ($node->hasAttribute('class')) {
        } else {
            $node->setAttribute(
                'class',
                new PhpNode(
                    'if ($this->isCurrent(' . PhpNode::expr($value)->code . ')) echo \'current\';',
                    true
                )
            );
        }
        $node->setAttribute('href', new PhpNode('$this->link(' . PhpNode::expr($value)->code . ')'));
    }

    /**
     * Sets the src-attribute to the specified route-value (see {@see \Jivoo\Routing\Routing}).
     * @param HtmlNode $node Node.
     * @param TemplateNode|null $value Macro parameter.
     */
    public function srcMacro(HtmlNode $node, TemplateNode $value)
    {
        $node->setAttribute('src', new PhpNode('$this->link(' . PhpNode::expr($value)->code . ')'));
    }

    /**
     * Adds a class.
     * @param HtmlNode $node Node.
     * @param TemplateNode|null $value Macro parameter.
     */
    public function classMacro(HtmlNode $node, TemplateNode $value)
    {
        if ($node->hasAttribute('class')) {
            $node->setAttribute(
                'class',
                new PhpNode("'" . \Jivoo\View\Html::h($node->getAttribute('class')) . " ' . " . PhpNode::expr($value)->code)
            );
        } else {
            $node->setAttribute('class', PhpNode::expr($value));
        }
    }

    /**
     * Points the src- or href-attribute at an asset.
     * @param HtmlNode $node Node.
     * @param TemplateNode|null $value Macro parameter.
     */
    public function fileMacro(HtmlNode $node, TemplateNode $value = null)
    {
        // TODO: implement
    }

    /**
     * Translates content of node, automatically replaces expressions with placeholders.
     * @param HtmlNode $node Node.
     * @param TemplateNode|null $value Macro parameter.
     */
    public function trMacro(HtmlNode $node, TemplateNode $value = null)
    {
        $translate = '';
        $num = 1;
        $params = array();
        $before = array();
        foreach ($node->getChildren() as $child) {
            if ($child instanceof TextNode) {
                $translate .= $child->text;
            } elseif ($child instanceof PhpNode and ! $child->statement) {
                $translate .= '%' . $num;
                $params[] = $child->code;
                $num++;
            } else {
                $translate .= '%' . $num;
                $params[] = PhpNode::expr($child);
                $num++;
            }
        }
        if (count($params) == 0) {
            $params = '';
        } else {
            $params = ', ' . implode(', ', $params);
        }
        $translate = trim($translate);
        $node->clear();
        $phpNode = new PhpNode('Jivoo\I18n\I18n::get(' . var_export($translate, true) . $params . ')');
        $node->append($phpNode);
    }

    /**
     * Translates content of node, automatically replaces expressions with placeholders.
     * Expects content of node to be plural and macro parameter to be singular.
     * @param HtmlNode $node Node.
     * @param TemplateNode|null $value Macro parameter.
     */
    public function tnMacro(HtmlNode $node, TemplateNode $value)
    {
        $translate = '';
        $num = 1;
        $params = array();
        $before = array();
        foreach ($node->getChildren() as $child) {
            if ($child instanceof TextNode) {
                $translate .= $child->text;
            } elseif ($child instanceof PhpNode and ! $child->statement) {
                $translate .= '%' . $num;
                $params[] = $child->code;
                $num++;
            } else {
                $translate .= '%' . $num;
                $params[] = PhpNode::expr($child);
                $num++;
            }
        }
        if (count($params) == 0) {
            $params = '';
        } else {
            $params = ', ' . implode(', ', $params);
        }
        $translate = trim($translate);
        $node->clear();
        $phpNode = new PhpNode(
            'Jivoo\I18n\I18n::nget(' . var_export($translate, true) . ', ' . PhpNode::expr($value)->code . $params . ')'
        );
        $node->append($phpNode);
    }
}
