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

namespace phpDocumentor\Renderer {

    use Mockery as m;
    use phpDocumentor\Renderer\Action\TestAction1;
    use phpDocumentor\Renderer\Action\TestAction2;
    use phpDocumentor\Renderer\Template\Parameter;

    /**
     * Tests the functionality for the TemplateFactory class.
     * @coversDefaultClass phpDocumentor\Renderer\TemplateFactory
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
                // Any name without slashes resolves to \phpDocumentor\Renderer\Action\ + class name
                [ 'name' => 'TestAction1' ],
                // or we can just provide the FQCN
                [ 'name' => '\phpDocumentor\Renderer\Action\TestAction1' ],
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
         * @uses phpDocumentor\Renderer\Template\Parameter
         * @uses phpDocumentor\Renderer\Template
         */
        public function testCreateTemplateFromOptionsArray()
        {
            $this->markTestIncomplete(
                'Templates cannot be asserted; more fine-grained assertions are needed to make this work'
            );
            $fixture = new TemplateFactory([]);
            $renderPass = m::mock(RenderPass::class);
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
         * @uses phpDocumentor\Renderer\Template
         */
        public function testCreatingATemplateWithoutParametersAndActions()
        {
            $fixture = new TemplateFactory([]);
            $template = $fixture->create(m::mock(RenderPass::class), [ 'name' => 'TemplateName' ]);

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
            $fixture = new TemplateFactory([]);
            $fixture->create(m::mock(RenderPass::class), []);
        }

        /**
         * @expectedException \InvalidArgumentException
         * @covers ::create
         * @covers ::<!public>
         */
        public function testIfErrorIsThrownIfNameIsNotAString()
        {
            $fixture = new TemplateFactory([]);
            $fixture->create(m::mock(RenderPass::class), ['name' => true]);
        }

        /**
         * @expectedException \InvalidArgumentException
         * @covers ::create
         * @covers ::<!public>
         * @uses phpDocumentor\Renderer\Template
         */
        public function testIfErrorIsThrownIfTemplateParametersIsNotAnArray()
        {
            $fixture = new TemplateFactory([]);
            $fixture->create(m::mock(RenderPass::class), ['name' => 'TemplateName', 'parameters' => ['bla']]);
        }

        /**
         * @expectedException \InvalidArgumentException
         * @covers ::create
         * @covers ::<!public>
         * @uses phpDocumentor\Renderer\Template
         */
        public function testIfErrorIsThrownIfActionsIsNotAnArray()
        {
            $fixture = new TemplateFactory([]);
            $fixture->create(m::mock(RenderPass::class), ['name' => 'TemplateName', 'actions' => ['bla']]);
        }

        /**
         * @expectedException \InvalidArgumentException
         * @covers ::create
         * @covers ::<!public>
         * @uses phpDocumentor\Renderer\Template
         */
        public function testIfErrorIsThrownIfTemplateParametersDoesNotHaveAKey()
        {
            $fixture = new TemplateFactory([]);
            $fixture->create(
                m::mock(RenderPass::class),
                ['name' => 'TemplateName', 'parameters' => [['value' => 'value1']]]
            );
        }

        /**
         * @expectedException \InvalidArgumentException
         * @covers ::create
         * @covers ::<!public>
         * @uses phpDocumentor\Renderer\Template
         */
        public function testIfErrorIsThrownIfTemplateParametersDoesNotHaveAValue()
        {
            $fixture = new TemplateFactory([]);
            $fixture->create(
                m::mock(RenderPass::class),
                ['name' => 'TemplateName', 'parameters' => [['key' => 'key1']]]
            );
        }

        /**
         * @expectedException \InvalidArgumentException
         * @covers ::create
         * @covers ::<!public>
         * @uses phpDocumentor\Renderer\Template
         */
        public function testIfErrorIsThrownIfActionClassDoesNotExist()
        {
            $fixture = new TemplateFactory([]);
            $fixture->create(
                m::mock(RenderPass::class),
                ['name' => 'TemplateName', 'actions' => [['name' => 'TestAction3']]]
            );
        }

        /**
         * @expectedException \InvalidArgumentException
         * @covers ::create
         * @covers ::<!public>
         * @uses phpDocumentor\Renderer\Template
         */
        public function testIfErrorIsThrownIfActionClassDoesNotImplementActionInterface()
        {
            $fixture = new TemplateFactory([]);
            $fixture->create(
                m::mock(RenderPass::class),
                ['name' => 'TemplateName', 'actions' => [['name' => 'TestAction4']]]
            );
        }

        /**
         * @expectedException \RuntimeException
         * @covers ::create
         * @covers ::<!public>
         * @uses phpDocumentor\Renderer\Template
         */
        public function testIfErrorIsThrownIfActionFactoryMethodReturnsNothing()
        {
            $fixture = new TemplateFactory([]);
            $fixture->create(
                m::mock(RenderPass::class),
                ['name' => 'TemplateName', 'actions' => [['name' => 'TestAction5']]]
            );
        }
    }
}

namespace phpDocumentor\Renderer\Action {

    use phpDocumentor\Renderer\Action;
    use phpDocumentor\Renderer\Template;

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
