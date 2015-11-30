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

use Mockery as m;
use Particle\Validator\Failure;
use Particle\Validator\ValidationResult;
use Particle\Validator\Validator;
use phpDocumentor\Reflection\Php\Factory\File\Adapter;
use phpDocumentor\Reflection\Php\File;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategies;
use phpDocumentor\Reflection\Php\Factory\File\CreateCommand;

/**
 * @coversDefaultClass phpDocumentor\Reflection\Php\Validation\ValidationMiddleware
 */
class ValidationMiddlewareTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var m\MockInterface
     */
    private $validatorMock;

    /**
     * @var ValidationMiddleware
     */
    private $fixture;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     */
    protected function setUp()
    {
        $this->validatorMock = m::mock(Validator::class);
        $this->fixture = new ValidationMiddleware($this->validatorMock, new FileExtractor());
    }

    /**
     * @covers ::execute
     * @covers ::__construct
     *
     * @uses phpDocumentor\Reflection\Php\Validation\ValidatedFile
     */
    public function testExecute()
    {
        $validationResult = new ValidationResult(
            false,
            [
                new Failure('class.Name', 'PHPDOC_TEST', 'no doc', [])
            ],
            []
        );

        $createCommand = new CreateCommand(m::mock(Adapter::class), 'someFile.php', new ProjectFactoryStrategies([]));
        $this->validatorMock->shouldReceive('validate')
            ->andReturn(
                $validationResult
            );

        $result = $this->fixture->execute($createCommand, function() { return new File('hash', 'someFile.php'); });

        $this->assertFalse($result->isValid());
        $this->assertEquals([
            'class.Name' => [   
                'PHPDOC_TEST' => 'no doc'
            ]
        ], $result->getMessages());
    }
}
