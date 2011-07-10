<?php
/**
 * @category   DocBlox
 * @package    Transformer
 * @subpackage Behaviour
 * @author	   Stepan Anchugov <kix@kixlive.ru>
 * @license	   http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */

/**
 * Behaviour that adds support for @internal tag
 *
 * @category   DocBlox
 * @package    Transformer
 * @subpackage Behaviour
 * @author     Stepan Anchugov <kix@kixlive.ru>
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */
class DocBlox_Transformer_Behaviour_Tag_Internal extends
    DocBlox_Transformer_Behaviour_Tag_Ignore
{
    protected $tag = 'internal';

}