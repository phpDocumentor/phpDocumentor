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

namespace phpDocumentor\DependencyInjection;

use phpDocumentor\Configuration\Definition\Version3;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Symfony DI-extension configuration for {@see ApplicationExtension}.
 *
 * This class exposes the same tree as the phpdoc.xml v3 schema so that the
 * ApplicationExtension can:
 *  - validate and merge configs contributed via prepend() by any extension, and
 *  - store the result as the container parameter "phpdocumentor.config" before
 *    the container is compiled.
 *
 * Deliberately does NOT apply value-object normalisation (Dsn/Path objects).
 * All values are kept as plain strings/arrays so that they can safely live as
 * container parameters.  Value-object creation remains in the service layer
 * (ConfigurationFactory::createConfigurationFromArray()).
 */
final class Configuration implements ConfigurationInterface
{
    /** The default template name used when none is specified in phpdoc.xml. */
    public const DEFAULT_TEMPLATE_NAME = 'default';

    public function getConfigTreeBuilder(): TreeBuilder
    {
        // Re-use the Version3 tree definition.  The "default" template name is
        // only used to fill in the default value for the <template> node when
        // none is present in the XML; the actual runtime default is resolved
        // later by the service-layer ConfigurationFactory.
        return (new Version3(self::DEFAULT_TEMPLATE_NAME))->getConfigTreeBuilder();
    }
}
