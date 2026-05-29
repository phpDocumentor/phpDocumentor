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
use phpDocumentor\ProjectSettings;
use PHPUnit\Framework\TestCase;

/** @coversDefaultClass \phpDocumentor\ProjectSettings */
final class SettingsTest extends TestCase
{
    public function testItCanKeepTrackWhetherSourceIsIncluded(): void
    {
        $settings = new ProjectSettings();

        $this->assertFalse($settings->shouldIncludeSource());

        $settings->includeSource();

        $this->assertTrue($settings->shouldIncludeSource());

        $settings->excludeSource();

        $this->assertFalse($settings->shouldIncludeSource());
    }

    public function testDetectSettingsAreModifiedWhenChangingInclusionOfSource(): void
    {
        $settings = new ProjectSettings();

        $this->assertFalse($settings->isModified());

        $settings->includeSource();

        $this->assertTrue($settings->isModified());
    }

    public function testItCanKeepTrackWhetherVisibilityIsSpecified(): void
    {
        $settings = new ProjectSettings();

        $this->assertSame(ApiSpecification::VISIBILITY_DEFAULT, $settings->getVisibility());

        $settings->setVisibility(ApiSpecification::VISIBILITY_PUBLIC);

        $this->assertSame(ApiSpecification::VISIBILITY_PUBLIC, $settings->getVisibility());
    }

    public function testDetectSettingsAreModifiedWhenChangingVisibility(): void
    {
        $settings = new ProjectSettings();

        $this->assertFalse($settings->isModified());

        $settings->setVisibility(ApiSpecification::VISIBILITY_PUBLIC);

        $this->assertTrue($settings->isModified());
    }

    public function testItCanStoreCustomSettings(): void
    {
        $settings = new ProjectSettings();

        $this->assertSame([], $settings->getCustom());

        $settings->setCustom(['key' => 'value']);

        $this->assertSame(['key' => 'value'], $settings->getCustom());
    }

    public function testDetectSettingsAreModifiedWhenSettingNewCustomSettings(): void
    {
        $settings = new ProjectSettings();

        $this->assertFalse($settings->isModified());

        $settings->setCustom(['key' => 'value']);

        $this->assertTrue($settings->isModified());
    }

    public function testThatTheModifiedFlagCanBeReset(): void
    {
        $settings = new ProjectSettings();

        $this->assertFalse($settings->isModified());

        $settings->includeSource();

        $this->assertTrue($settings->isModified());

        $settings->clearModifiedFlag();

        $this->assertFalse($settings->isModified());
    }
}
