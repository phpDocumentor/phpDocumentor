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

namespace phpDocumentor\Reflection\Middleware;


use League\Event\Emitter;
use phpDocumentor\Validation\Validator;
use phpDocumentor\Reflection\Php\Factory\File\CreateCommand;

final class ValidatingMiddleware implements Middleware
{
    /**
     * @var Validator
     */
    private $validator;
    /**
     * @var Emitter
     */
    private $emitter;

    /**
     * ValidatingMiddleware constructor.
     * @param Validator $validator
     * @param Emitter $emitter
     */
    public function __construct(Validator $validator, Emitter $emitter)
    {
        $this->validator = $validator;
        $this->emitter = $emitter;
    }

    /**
     * Executes this middle ware class.
     *
     * @param $command
     * @param callable $next
     *
     * @return object
     */
    public function execute($command, callable $next)
    {
        try {
            $result = $next($command);
        } catch (\Exception $e) {
            var_dump($command);
        }
        if ($command instanceof CreateCommand) {
            $validationResult = $this->validator->validate($result);
            $this->emitter->emit(new Validated($result->getPath(), $validationResult));
        }

        return $result;
    }
}
