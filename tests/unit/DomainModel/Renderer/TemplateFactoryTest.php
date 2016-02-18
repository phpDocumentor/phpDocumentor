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

namespace phpDocumentor\DomainModel\Renderer {

    use Mockery as m;
    use phpDocumentor\DomainModel\Renderer\Template;
    use phpDocumentor\DomainModel\Renderer\RenderContext;
    use phpDocumentor\Infrastructure\Renderer\XmlTemplateFactory;
    use phpDocumentor\Application\Renderer\Action\TestAction1;
    use phpDocumentor\Application\Renderer\Action\TestAction2;
    use phpDocumentor\DomainModel\Renderer\Template\Parameter;

    /**
     * Tests the functionality for the TemplateFactory class.
     * @coversDefaultClass phpDocumentor\DomainModel\Renderer\TemplateFactory
     */
    class TemplateFactoryTest extends \PHPUnit_Framework_TestCase
    {
        private $exampleOptionsArray = [
            'name' => 'TemplateName',
            'parameters' => [
                ['key' => 'Parameter1', 'value' => 'Value1'],
                ['key' => 'Parameter2', 'value' => 'Value2'],
            ],
            'actions' => [
                // Any name without slashes resolves to \phpDocumentor\Application\Renderer\Action\ + class name
                [ 'name' => 'TestAction1' ],
                // or we can just provide the FQCN
                [ 'name' => '\phpDocumentor\Application\Renderer\Action\TestAction1' ],
                [
                    'name' => 'TestAction2',
                    'parameters' => [
                        ['key' => 'Parameter2', 'value' => 'Value3'],
                        ['key' => 'Parameter3', 'value' => 'Value4']
                    ]
                ]
            ]
        ];

        /**
         * @covers ::create
         * @covers ::<!public>
         */
        public function testCreateTemplateFromOptionsArray()
        {
            $this->markTestIncomplete(
                'Templates cannot be asserted; more fine-grained assertions are needed to make this work'
            );
            $fixture = new XmlTemplateFactory([]);
            $renderPass = m::mock(RenderContext::class);
            $template = $fixture->create($renderPass, $this->exampleOptionsArray);

            $expectedTemplate = new Template(
                'TemplateName',
                [new Parameter('Parameter1', 'Value1'), new Parameter('Parameter2', 'Value2')],
                [
                    new TestAction1([
                        'Parameter1' => new Parameter('Parameter1', 'Value1'),
                        'Parameter2' => new Parameter('Parameter2', 'Value2'),
                        'renderPass' => new Parameter('renderPass', $renderPass),
                        'template' => new Parameter('template', 'Value2')
                    ]),
                    new TestAction1([
                        'Parameter1' => new Parameter('Parameter1', 'Value1'),
                        'Parameter2' => new Parameter('Parameter2', 'Value2'),
                        'renderPass' => new Parameter('renderPass', $renderPass),
                        'template' => new Parameter('template', 'Value2')
                    ]),
                    new TestAction2([
                        'Parameter1' => new Parameter('Parameter1', 'Value1'),
                        // verify that Parameter2 is overridden; hence the value Value3
                        'Parameter2' => new Parameter('Parameter2', 'Value3'),
                        'Parameter3' => new Parameter('Parameter3', 'Value4'),
                        'renderPass' => new Parameter('renderPass', $renderPass),
                        'template' => new Parameter('template', 'Value2')
                    ]),
                ]
            );

            $this->assertEquals($expectedTemplate, $template);
        }

        /**
         * @covers ::create
         * @covers ::<!public>
         */
        public function testCreatingATemplateWithoutParametersAndActions()
        {
            $fixture = new XmlTemplateFactory([]);
            $template = $fixture->create(m::mock(RenderContext::class), [ 'name' => 'TemplateName' ]);

            $expectedTemplate = new Template('TemplateName');

            $this->assertEquals($expectedTemplate, $template);
        }

        /**
         * @expectedException \InvalidArgumentException
         * @covers ::create
         * @covers ::<!public>
         */
        public function testIfErrorIsThrownIfNameIsNotProvided()
        {
            $fixture = new XmlTemplateFactory([]);
            $fixture->create(m::mock(RenderContext::class), []);
        }

        /**
         * @expectedException \InvalidArgumentException
         * @covers ::create
         * @covers ::<!public>
         */
        public function testIfErrorIsThrownIfNameIsNotAString()
        {
            $fixture = new XmlTemplateFactory([]);
            $fixture->create(m::mock(RenderContext::class), ['name' => true]);
        }

        /**
         * @expectedException \InvalidArgumentException
         * @covers ::create
         * @covers ::<!public>
         */
        public function testIfErrorIsThrownIfTemplateParametersIsNotAnArray()
        {
            $fixture = new XmlTemplateFactory([]);
            $fixture->create(m::mock(RenderContext::class), ['name' => 'TemplateName', 'parameters' => ['bla']]);
        }

        /**
         * @expectedException \InvalidArgumentException
         * @covers ::create
         * @covers ::<!public>
         */
        public function testIfErrorIsThrownIfActionsIsNotAnArray()
        {
            $fixture = new XmlTemplateFactory([]);
            $fixture->create(m::mock(RenderContext::class), ['name' => 'TemplateName', 'actions' => ['bla']]);
        }

        /**
         * @expectedException \InvalidArgumentException
         * @covers ::create
         * @covers ::<!public>
         */
        public function testIfErrorIsThrownIfTemplateParametersDoesNotHaveAKey()
        {
            $fixture = new XmlTemplateFactory([]);
            $fixture->create(
                m::mock(RenderContext::class),
                ['name' => 'TemplateName', 'parameters' => [['value' => 'value1']]]
            );
        }

        /**
         * @expectedException \InvalidArgumentException
         * @covers ::create
         * @covers ::<!public>
         */
        public function testIfErrorIsThrownIfTemplateParametersDoesNotHaveAValue()
        {
            $fixture = new XmlTemplateFactory([]);
            $fixture->create(
                m::mock(RenderContext::class),
                ['name' => 'TemplateName', 'parameters' => [['key' => 'key1']]]
            );
        }

        /**
         * @expectedException \InvalidArgumentException
         * @covers ::create
         * @covers ::<!public>
         */
        public function testIfErrorIsThrownIfActionClassDoesNotExist()
        {
            $fixture = new XmlTemplateFactory([]);
            $fixture->create(
                m::mock(RenderContext::class),
                ['name' => 'TemplateName', 'actions' => [['name' => 'TestAction3']]]
            );
        }

        /**
         * @expectedException \InvalidArgumentException
         * @covers ::create
         * @covers ::<!public>
         */
        public function testIfErrorIsThrownIfActionClassDoesNotImplementActionInterface()
        {
            $fixture = new XmlTemplateFactory([]);
            $fixture->create(
                m::mock(RenderContext::class),
                ['name' => 'TemplateName', 'actions' => [['name' => 'TestAction4']]]
            );
        }

        /**
         * @expectedException \RuntimeException
         * @covers ::create
         * @covers ::<!public>
         */
        public function testIfErrorIsThrownIfActionFactoryMethodReturnsNothing()
        {
            $fixture = new XmlTemplateFactory([]);
            $fixture->create(
                m::mock(RenderContext::class),
                ['name' => 'TemplateName', 'actions' => [['name' => 'TestAction5']]]
            );
        }
    }
}

// @codingStandardsIgnoreStart
namespace phpDocumentor\Application\Renderer\Template\Action {

    use phpDocumentor\DomainModel\Renderer\Template\Action;
    use phpDocumentor\DomainModel\Renderer\Template;

    class TestAction1 implements Action
    {
        private $parameters;

        public function __construct(array $parameters)
        {
            $this->parameters = $parameters;
        }

        public static function create(array $parameters)
        {
            return new static($parameters);
        }

        public function __toString()
        {
            return __CLASS__;
        }
    }

    class TestAction2 extends TestAction1
    {
    }

    class TestAction4
    {
    }

    class TestAction5 implements Action
    {
        /**
         * @param Template\Parameter[] $parameters
         *
         * @return static
         */
        public static function create(array $parameters)
        {
            return null;
        }

        public function __toString()
        {
            return __CLASS__;
        }
    }
}
// @codingStandardsIgnoreEnd
