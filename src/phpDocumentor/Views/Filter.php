<?php

namespace phpDocumentor\Views;

interface Filter
{
    public function __invoke($data);
}
