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

namespace phpDocumentor\Descriptor\Builder;

/**
 * Attempts to retrieve an Assembler for the provided criteria.
 */
class AssemblerFactory
{
    /** @var mixed[]  */
    protected $assemblers = array();

    /**
     * Registers an assembler instance to this factory.
     *
     * @param callable           $matcher   A callback function accepting the criteria as only parameter and which must
     *     return a boolean.
     * @param AssemblerInterface $assembler An instance of the Assembler that will be returned if the callback returns
     *     true with the provided criteria.
     *
     * @return void
     */
    public function register($matcher, AssemblerInterface $assembler)
    {
        $this->assemblers[] = array(
            'matcher'   => $matcher,
            'assembler' => $assembler
        );
    }

    /**
     * Retrieves a matching Assembler based on the provided criteria or null if none was found.
     *
     * @param mixed $criteria
     *
     * @return AssemblerInterface|null
     */
    public function get($criteria)
    {
        foreach ($this->assemblers as $candidate) {
            $matcher = $candidate['matcher'];
            if ($matcher($criteria) === true) {
                return $candidate['assembler'];
            }
        }

        return null;
    }
}
