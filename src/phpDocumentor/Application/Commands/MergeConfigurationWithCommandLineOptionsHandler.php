<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2015 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Application\Commands;

use phpDocumentor\Configuration;

final class MergeConfigurationWithCommandLineOptionsHandler
{
    public function __invoke(MergeConfigurationWithCommandLineOptions $command)
    {
        $configuration = $command->getConfiguration();
        $input = $command->getOptions();

        $this->overwriteConfigurationSetting($input, $configuration->getFiles(), 'filename', 'Files');
        $this->overwriteConfigurationSetting($input, $configuration->getFiles(), 'directory', 'Directories');
        $this->overwriteConfigurationSetting($input, $configuration->getParser(), 'target');
        $this->overwriteConfigurationSetting($input, $configuration->getParser(), 'encoding');
        $this->overwriteConfigurationSetting($input, $configuration->getParser(), 'extensions');
        $this->overwriteConfigurationSetting($input, $configuration->getFiles(), 'ignore');
        $this->overwriteConfigurationSetting($input, $configuration->getFiles(), 'ignore-hidden', 'IgnoreHidden');
        $this->overwriteConfigurationSetting($input, $configuration->getFiles(), 'ignore-symlinks', 'IgnoreSymlinks');
        $this->overwriteConfigurationSetting($input, $configuration->getParser(), 'markers');
        $this->overwriteConfigurationSetting($input, $configuration, 'title');
        $this->overwriteConfigurationSetting($input, $configuration->getParser(), 'force', 'ShouldRebuildCache');
        $this->overwriteConfigurationSetting(
            $input,
            $configuration->getParser(),
            'defaultpackagename',
            'DefaultPackageName'
        );

        $this->overwriteConfigurationSetting($input, $configuration->getParser(), 'visibility');
        if ($input['parseprivate']) {
            $configuration->getParser()->setVisibility($configuration->getParser()->getVisibility() . ',internal');
        }
        if (! $configuration->getParser()->getVisibility()) {
            $configuration->getParser()->setVisibility('default');
        }

        $this->fixFilesConfiguration($configuration);

        $arguments = $command->getArguments();
        if (isset($arguments['paths']) && is_array($arguments['paths'])) {
            foreach ($arguments['paths'] as $path) {
                $this->addPathToConfiguration($path, $configuration);
            }
        }
    }

    /**
     * Overwrites a configuration option with the given option from the input if it was passed.
     *
     * @param string[]       $input
     * @param object         $section               The configuration (sub)object to modify
     * @param string         $optionName            The name of the option to read from the input.
     * @param string|null    $configurationItemName when omitted the optionName is used where the first letter
     *     is uppercased.
     *
     * @return void
     */
    private function overwriteConfigurationSetting($input, $section, $optionName, $configurationItemName = null)
    {
        if ($configurationItemName === null) {
            $configurationItemName = ucfirst($optionName);
        }

        if (isset($input[$optionName]) && $input[$optionName]) {
            $section->{'set' . $configurationItemName}($input[$optionName]);
        }
    }

    /**
     * The files configuration node has moved, this method provides backwards compatibility for phpDocumentor 3.
     *
     * We add the files configuration because it should actually belong there, simplifies the interface but
     * removing it is a rather serious BC break. By using a non-serialized setter/property in the parser config
     * and setting the files config on it we can simplify this interface.
     *
     * @param Configuration $configuration
     *
     * @deprecated to be removed in phpDocumentor 4
     *
     * @return void
     */
    private function fixFilesConfiguration(Configuration $configuration)
    {
        if (! $configuration->getParser()->getFiles() && $configuration->getFiles()) {
            trigger_error(
                'Your source files and directories should be declared in the "parser" node of your configuration but '
                . 'was found in the root of your configuration. This is deprecated starting with phpDocumentor 3 and '
                . 'will be removed with phpDocumentor 4.',
                E_USER_DEPRECATED
            );

            $configuration->getParser()->setFiles($configuration->getFiles());
            $configuration->setFiles(null);
        }
    }

    /**
     * Adds the given path to the Files or Directories section of the configuration depending on whether it is a file
     * or folder.
     *
     * @param string        $path
     * @param Configuration $configuration
     *
     * @return void
     */
    private function addPathToConfiguration($path, $configuration)
    {
        $fileInfo = new \SplFileInfo($path);
        if ($fileInfo->isDir()) {
            $directories   = $configuration->getParser()->getFiles()->getDirectories();
            $directories[] = $path;
            $configuration->getParser()->getFiles()->setDirectories($directories);
        } else {
            $files   = $configuration->getParser()->getFiles()->getFiles();
            $files[] = $path;
            $configuration->getParser()->getFiles()->setFiles($files);
        }
    }
}
