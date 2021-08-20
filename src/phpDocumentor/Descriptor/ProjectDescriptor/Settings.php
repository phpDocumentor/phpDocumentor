<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Descriptor\ProjectDescriptor;

use phpDocumentor\Configuration\ApiSpecification;

/**
 * Contains the Settings for the current Project.
 */
final class Settings
{
    /** @var bool Represents whether this settings object has been modified */
    private $isModified = false;

    /** @var int a bitflag representing which visibilities are contained and allowed in this project */
    private $visibility = ApiSpecification::VISIBILITY_DEFAULT;

    /** @var bool */
    private $includeSource = false;

    /**
     * A flexible list of settings that can be used by Writers, templates and more as additional settings.
     *
     * @var (string|bool)[]
     */
    private $custom = [];

    /**
     * Stores the visibilities that are allowed to be executed as a bitflag.
     *
     * @param int $visibilityFlag A bitflag combining the VISIBILITY_* constants.
     */
    public function setVisibility(int $visibilityFlag): void
    {
        $this->setValueAndCheckIfModified('visibility', $visibilityFlag);
    }

    /**
     * Returns the bit flag representing which visibilities are allowed.
     *
     * @see self::isVisibilityAllowed() for a convenience method to easily check against a specific visibility.
     */
    public function getVisibility(): int
    {
        return $this->visibility;
    }

    /**
     * Returns whether one of the values of this object was modified.
     */
    public function isModified(): bool
    {
        return $this->isModified;
    }

    /**
     * Resets the flag indicating whether the settings have changed.
     */
    public function clearModifiedFlag(): void
    {
        $this->isModified = false;
    }

    public function includeSource(): void
    {
        $this->setValueAndCheckIfModified('includeSource', true);
    }

    public function excludeSource(): void
    {
        $this->setValueAndCheckIfModified('includeSource', false);
    }

    public function shouldIncludeSource(): bool
    {
        return $this->includeSource;
    }

    /**
     * A flexible list of settings that can be used by Writers, templates and more as additional settings.
     *
     * Some writers or templates can have their own specific settings; this can be registered here and accessed in
     * various locations through the accessor {@see ProjectDescriptor::getSettings()} or in the templates using
     * the `project.settings.other` variable.
     *
     * @return array<string, bool|string>
     */
    public function getCustom(): array
    {
        return $this->custom;
    }

    /**
     * @param array<string, bool|string> $settings
     */
    public function setCustom(array $settings): void
    {
        $this->setValueAndCheckIfModified('custom', $settings);
    }

    /**
     * Sets a property's value and if it differs from the previous then mark these settings as modified.
     *
     * @param int|bool|array<string, bool|string> $value
     */
    private function setValueAndCheckIfModified(string $propertyName, $value): void
    {
        if ($this->{$propertyName} !== $value) {
            $this->isModified = true;
        }

        $this->{$propertyName} = $value;
    }
}
