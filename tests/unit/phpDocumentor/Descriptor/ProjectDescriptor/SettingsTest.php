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
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \phpDocumentor\Descriptor\ProjectDescriptor\Settings
 * @covers ::<private>
 */
final class SettingsTest extends TestCase
{
    /**
     * @covers ::getMarkers
     * @covers ::setMarkers
     */
    public function testKeepingTrackOfMarkers() : void
    {
        $expected = ['TODO', 'FIXME'];

        $settings = new Settings();

        $this->assertSame([], $settings->getMarkers());

        $settings->setMarkers($expected);

        $this->assertSame($expected, $settings->getMarkers());
    }

    /**
     * @covers ::shouldIncludeSource
     * @covers ::includeSource
     * @covers ::excludeSource
     */
    public function testItCanKeepTrackWhetherSourceIsIncluded() : void
    {
        $settings = new Settings();

        $this->assertFalse($settings->shouldIncludeSource());

        $settings->includeSource();

        $this->assertTrue($settings->shouldIncludeSource());

        $settings->excludeSource();

        $this->assertFalse($settings->shouldIncludeSource());
    }

    /**
     * @covers ::isModified
     */
    public function testDetectSettingsAreModifiedWhenChangingInclusionOfSource() : void
    {
        $settings = new Settings();

        $this->assertFalse($settings->isModified());

        $settings->includeSource();

        $this->assertTrue($settings->isModified());
    }

    /**
     * @covers ::getVisibility
     * @covers ::setVisibility
     */
    public function testItCanKeepTrackWhetherVisibilityIsSpecified() : void
    {
        $settings = new Settings();

        $this->assertSame(ApiSpecification::VISIBILITY_DEFAULT, $settings->getVisibility());

        $settings->setVisibility(ApiSpecification::VISIBILITY_PUBLIC);

        $this->assertSame(ApiSpecification::VISIBILITY_PUBLIC, $settings->getVisibility());
    }

    /**
     * @covers ::isModified
     */
    public function testDetectSettingsAreModifiedWhenChangingVisibility() : void
    {
        $settings = new Settings();

        $this->assertFalse($settings->isModified());

        $settings->setVisibility(ApiSpecification::VISIBILITY_PUBLIC);

        $this->assertTrue($settings->isModified());
    }

    /**
     * @covers ::getCustom
     * @covers ::setCustom
     */
    public function testItCanStoreCustomSettings() : void
    {
        $settings = new Settings();

        $this->assertSame([], $settings->getCustom());

        $settings->setCustom(['key' => 'value']);

        $this->assertSame(['key' => 'value'], $settings->getCustom());
    }

    /**
     * @covers ::isModified
     */
    public function testDetectSettingsAreModifiedWhenSettingNewCustomSettings() : void
    {
        $settings = new Settings();

        $this->assertFalse($settings->isModified());

        $settings->setCustom(['key' => 'value']);

        $this->assertTrue($settings->isModified());
    }

    /**
     * @covers ::clearModifiedFlag
     */
    public function testThatTheModifiedFlagCanBeReset() : void
    {
        $settings = new Settings();

        $this->assertFalse($settings->isModified());

        $settings->includeSource();

        $this->assertTrue($settings->isModified());

        $settings->clearModifiedFlag();

        $this->assertFalse($settings->isModified());
    }
}
