<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Plugin\Core\Transformer\Writer;

use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Transformer\Template;
use phpDocumentor\Transformer\Transformation;

/**
 */
class Pdf extends Twig
{
    /** @var string destination */
    protected $destinationPath;

    /**
     * This method combines the ProjectDescriptor and the given target template
     * and creates a static html page at the artifact location.
     *
     * @param ProjectDescriptor $project        Document containing the structure.
     * @param Transformation    $transformation Transformation to execute.
     *
     * @return void
     */
    public function transform(ProjectDescriptor $project, Transformation $transformation)
    {
        parent::transform($project, $transformation);

        $dompdf = new \DOMPDF();
        $dompdf->load_html(file_get_contents($this->destinationPath));
        $dompdf->render();

        file_put_contents(
            $transformation->getTransformer()->getTarget() . DIRECTORY_SEPARATOR . $transformation->getArtifact(),
            $dompdf->output()
        );
    }

    /**
     * Uses the currently selected node and transformation to assemble the destination path for the file.
     *
     * @param DescriptorAbstract $node
     * @param Transformation     $transformation
     *
     * @return string|false returns the destination location or false if generation should be aborted.
     */
    protected function getDestinationPath($node, Transformation $transformation)
    {
        $this->destinationPath =
            $this->destinationPath ?: sys_get_temp_dir() . DIRECTORY_SEPARATOR .  md5($node->getName());

        return $this->destinationPath;
    }
}
