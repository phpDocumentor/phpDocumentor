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

namespace phpDocumentor\DomainModel;

use phpDocumentor\DomainModel\Parser\DocumentationFactory;
use phpDocumentor\DomainModel\Parser\DocumentationRepository;

final class ParseHandler
{
    /** @var DocumentationFactory */
    private $factory;

    /** @var DocumentationRepository */
    private $repository;

    public function __construct(DocumentationFactory $factory, DocumentationRepository $repository)
    {

        $this->factory = $factory;
        $this->repository = $repository;
    }

    public function __invoke(Parse $command)
    {
        $documentation = $this->factory->create($command->definition());
        $this->repository->save($documentation);
    }
}
