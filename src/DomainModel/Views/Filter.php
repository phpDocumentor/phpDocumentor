<?php

namespace phpDocumentor\DomainModel\Views;

interface Filter
{
    public function __invoke($data);
}
