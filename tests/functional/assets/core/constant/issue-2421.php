<?php

class Issue2421 {
    /**
     * Regular expression to check if a given identifier name is valid for use in PHP.
     *
     * @var string
     */
    const PHP_LABEL_REGEX = '`^[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*$`';
}
