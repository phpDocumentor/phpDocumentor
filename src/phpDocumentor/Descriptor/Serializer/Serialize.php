<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mvriel
 * Date: 2/2/13
 * Time: 9:02 PM
 * To change this template use File | Settings | File Templates.
 */

namespace phpDocumentor\Descriptor\Serializer;

use phpDocumentor\Descriptor\ProjectDescriptor;

class Serialize extends SerializerAbstract
{
    public function serialize(ProjectDescriptor $project)
    {
        return serialize($project);
    }

    public function unserialize($data)
    {
        return unserialize($data);
    }

    protected function getExtension()
    {
        return 'serialize';
    }
}