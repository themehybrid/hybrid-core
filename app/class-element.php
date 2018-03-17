<?php
/**
 * Element class.
 *
 * This class is used for building out HTML elements based on input
 * from developers. This gives us a consistent base, primarily for
 * building elements within the framework.
 *
 * @package   HybridCore
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2008 - 2017, Justin Tadlock
 * @link      https://themehybrid.com/hybrid-core
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Hybrid;

/**
 * Element class.
 *
 * @since  5.0.0
 * @access public
 */
class Element {

        /**
         * The HTML element tag name.
         *
         * @todo   Handle self-closing tags.
         * @since  5.0.0
         * @access protected
         * @var    string
         */
        protected $tag = '';

        /**
         * The content within the HTML tag.
         *
         * @since  5.0.0
         * @access protected
         * @var    string
         */
        protected $content = '';

        /**
         * Instance of `Attributes` for handling the element attributes.
         *
         * @since  5.0.0
         * @access public
         * @var    object|null
         */
        public $attr = null;

        /**
         * Array of self-closing tags (i.e., void elements) that have
         * no content.
         *
         * @since  5.0.0
         * @access protected
         * @var    array
         */
        protected $self_closing = [
                'base',
                'br',
                'col',
                'command',
                'embed',
                'hr',
                'img',
                'input',
                'keygen',
                'link',
                'menuitem',
                'meta',
                'param',
                'source',
                'track',
                'wbr'
        ];

        /**
         * Set up the object.
         *
         * @since  5.0.0
         * @access public
         * @param  string  $tag
         * @param  string  $content
         * @param  object  $attr
         * @return void
         */
        public function __construct( $tag, $content = '', Attributes $attr = null ) {

                $this->tag     = $tag;
                $this->content = $content;
                $this->attr    = $attr;
        }

        /**
         * Returns HTML element for output.
         *
         * @since  5.0.0
         * @access public
         * @return string
         */
        public function fetch() {

                $attr = $this->attr instanceof Attributes ? $this->attr->fetch() : '';

                if ( in_array( $this->tag, $this->self_closing ) ) {

                        return sprintf( '<%1$s %2$s />', tag_escape( $this->tag ), $attr );
                }

                return sprintf(
                        '<%1$s %2$s>%3$s</%1$s>',
                        tag_escape( $this->tag ),
                        $attr,
                        $this->content
                );
        }

        /**
         * Renders the HTML output.
         *
         * @since  5.0.0
         * @access public
         * @return void
         */
        public function render() {

                echo $this->fetch();
        }
}
