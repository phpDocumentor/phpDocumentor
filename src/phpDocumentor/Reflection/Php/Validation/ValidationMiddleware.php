<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2015 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Reflection\Php\Validation;

use Particle\Validator\Validator;
use phpDocumentor\Reflection\Middleware\Middleware;
use phpDocumentor\Reflection\Php\Factory\File\CreateCommand;
use phpDocumentor\Reflection\Php\File;

/**
 * Class ValidationMiddleware
 * @package phpDocumentor\Reflection\Php\Validation
 */
final class ValidationMiddleware implements Middleware
{
    /**
     * @var Validator
     */
    private $validator;

    /**
     * @var FileExtractor
     */
    private $fileExtractor;

    /**
     * ValidationMiddleware constructor.
     * @param Validator $validator
     * @param FileExtractor $fileExtractor
     */
    public function __construct(Validator $validator, FileExtractor $fileExtractor)
    {
        $this->validator = $validator;
        $this->fileExtractor = $fileExtractor;
    }

    /**
     * Executes this middle ware class.
     *
     * @param CreateCommand $command
     * @param callable $next
     *
     * @return object
     */
    public function execute($command, callable $next)
    {
        /** @var File $file */
        $file = $next($command);

        $values = $this->fileExtractor->extract($file);
        $result = $this->validator->validate($values);

        return new ValidatedFile($file, $result);
    }
}