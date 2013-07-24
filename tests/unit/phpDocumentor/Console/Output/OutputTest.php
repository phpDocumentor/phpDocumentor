<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2013 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */
 
namespace phpDocumentor\Console\Output;

use Monolog\Logger;
use Symfony\Component\Console\Output\ConsoleOutput;

use Mockery as m;

class ArgvOutputTest extends \PHPUnit_Framework_TestCase
{
    
    
        public function testSetAndGetLoggerWithString(){
            $output = new Output();
            $output->setLogger('Foo bar');
            
            $loggerOutput = $output->getLogger();
            
            $this->assertEquals('Foo bar', $loggerOutput);
            
        }
        
        public function testWriteTimedLog(){
            
            $message = "asjkdfnildbfhildsbidsbhivdsbhsdnFHIBAFSHLVBSDHFBHADSBFHIFBHVNSDIFBHI ANFIDBFHDBAFBAJSDBFHADSBFADSUFBADSJKFBASUFBJKADSBFGABFJKADSFBASGBHISABGHKAFBIGYA";
            $arguments = array("Foo", "Bar");
            
            
            $writeMock = m::mock('phpDocumentor\Console\Output');
            
            $writeMock->shouldDeferMissing();
            
            $writeMock->shouldReceive('write')->with(substr($message, 0, 68)." .. ");
            $writeMock->shouldReceive('writeln')->withAnyArgs();
            
            $writeTimedLogClass = new Output();
            
            $suit = $this;
            
            $writeTimedLogClass->writeTimedLog($message, function($operation, $arguments)use($suit){
            
                $suit->assertSame('Foo', $operation);
                $suit->assertSame('Bar', $arguments);
                
            }, $arguments);
            
        }


        public function testWriteLogger (){
            $this->markTestIncomplete();
            $outputInterface = m::mock('Symfony\Component\Console\Output\OutputInterface');
            
            $outputInterface->shouldIgnoreMissing();
            
            $formatterMock = m::mock('Symfony\Component\Console\Output\Output');
            $formatterMock->shouldDeferMissing();
            
            $formatterMock->setErrorOutput($outputInterface);
            
            
            $mock = m::mock('phpDocumentor\Console\Output\Output');
            $mock->shouldDeferMissing();
            $mock->setFormatter($formatterMock);
            
            $message = "Foo bar";
            $newline = true;

            $mock->shouldReceive('doWrite')->with($message, $newline);
            
            $mock->write($message, $newline, 0);
        }
}
