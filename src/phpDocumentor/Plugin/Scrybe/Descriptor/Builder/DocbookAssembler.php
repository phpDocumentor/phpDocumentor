<?php

namespace phpDocumentor\Plugin\Scrybe\Descriptor\Builder;

use phpDocumentor\Descriptor\Builder\AssemblerAbstract;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Plugin\Scrybe\Descriptor\DocbookDescriptor;

class DocbookAssembler extends AssemblerAbstract
{
    /**
     * Creates a Descriptor from the provided data.
     *
     * @param \ezcDocumentDocbook $data
     *
     * @return DescriptorAbstract|Collection
     */
    public function create($data)
    {
        $docbookDescriptor = new DocbookDescriptor();
        $docbookDescriptor->setPath($data->getPath());
        $docbookDescriptor->setContents($data->save());
        
        return $docbookDescriptor;
    }
}
