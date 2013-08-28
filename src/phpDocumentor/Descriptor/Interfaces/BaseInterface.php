<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mvriel
 * Date: 2/6/13
 * Time: 6:35 PM
 * To change this template use File | Settings | File Templates.
 */

namespace phpDocumentor\Descriptor\Interfaces;

use phpDocumentor\Descriptor\FileDescriptor;

interface BaseInterface
{
    /**
     * @param string $name
     *
     * @return void
     */
    public function setFullyQualifiedStructuralElementName($name);

    /**
     * @return string
     */
    public function getFullyQualifiedStructuralElementName();

    /**
     * @param string $name
     *
     * @return void
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $summary
     *
     * @return void
     */
    public function setSummary($summary);

    /**
     * @return string
     */
    public function getSummary();

    /**
     * @param string $description
     *
     * @return void
     */
    public function setDescription($description);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param FileDescriptor $file
     * @param int    $line
     *
     * @return void
     */
    public function setLocation(FileDescriptor $file, $line = 0);

    /**
     * @return string
     */
    public function getPath();

    /**
     * @return int
     */
    public function getLine();

    /**
     * @return Collection
     */
    public function getTags();
}
