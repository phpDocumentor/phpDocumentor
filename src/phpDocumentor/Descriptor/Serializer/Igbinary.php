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

class Igbinary extends SerializerAbstract
{
    public function serialize(ProjectDescriptor $project)
    {
        return igbinary_serialize($project);
    }

    public function unserialize($data)
    {
        return igbinary_unserialize($data);
    }

    protected function getExtension()
    {
        return 'igb';
    }
}