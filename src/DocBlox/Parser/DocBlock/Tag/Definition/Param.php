<?php
class DocBlox_Parser_DocBlock_Tag_Definition_Param extends DocBlox_Parser_DocBlock_Tag_Definition
{
    protected function configure()
    {
        if (trim($this->tag->getVariableName()) == '') {
            // TODO: get the name from the argument list
        }

        $this->xml['variable'] = $this->tag->getVariableName();
    }
}
