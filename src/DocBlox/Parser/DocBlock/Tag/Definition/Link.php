<?php
class DocBlox_Parser_DocBlock_Tag_Definition_Link extends DocBlox_Parser_DocBlock_Tag_Definition
{
    protected function configure()
    {
        if (!$this->tag instanceof DocBlox_Reflection_DocBlock_Tag_Link)
        {
            throw new InvalidArgumentException('Expected the tag to be for an @link');
        }

        $this->xml['link'] = $this->tag->getLink();
    }
}
