<?php

namespace phpDocumentor\DomainModel\ReadModel;

interface Filter
{
    public function __invoke($data);
}
