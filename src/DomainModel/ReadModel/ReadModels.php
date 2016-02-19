<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2016 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\DomainModel\ReadModel;

use Webmozart\Assert\Assert;

final class ReadModels extends \ArrayObject
{
    public function __construct($input = [])
    {
        Assert::allIsInstanceOf($input, ReadModel::class);

        parent::__construct($input);
    }

    public function offsetSet($index, $newval)
    {
        Assert::isInstanceOf($newval, ReadModel::class);

        parent::offsetSet($index, $newval);
    }
}
