<?php

/*
 * This file is part of the Fxp Composer Asset Plugin package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please views the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Composer\AssetPlugin\Installer;

use Composer\Composer;
use Composer\Installer\LibraryInstaller;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Composer\Util\Filesystem;
use Fxp\Composer\AssetPlugin\Config\Config;
use Fxp\Composer\AssetPlugin\Type\AssetTypeInterface;
use Fxp\Composer\AssetPlugin\Util\AssetPlugin;

/**
 * Installer for asset packages.
 *
 * @author Martin Hasoň <martin.hason@gmail.com>
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class AssetInstaller extends LibraryInstaller
{
    /**
     * @var Config
     */
    private $config;

    /**
     * Constructor.
     *
     * @param Config             $config
     * @param IOInterface        $io
     * @param Composer           $composer
     * @param AssetTypeInterface $assetType
     * @param Filesystem         $filesystem
     */
    public function __construct(Config $config, IOInterface $io, Composer $composer, AssetTypeInterface $assetType, Filesystem $filesystem = null)
    {
        parent::__construct($io, $composer, $assetType->getComposerType(), $filesystem);

        $this->config = $config;
        $paths = $this->config->getArray('installer-paths');

        if (!empty($paths[$this->type])) {
            $this->vendorDir = rtrim($paths[$this->type], '/');
        } else {
            $this->vendorDir = rtrim($this->vendorDir.'/'.$assetType->getComposerVendorName(), '/');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports($packageType)
    {
        return $packageType === $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function getInstallPath(PackageInterface $package)
    {
        $this->initializeVendorDir();

        $targetDir = $package->getTargetDir();

        list(, $name) = explode('/', $package->getPrettyName(), 2);

        return ($this->vendorDir ? $this->vendorDir.'/' : '').$name.($targetDir ? '/'.$targetDir : '');
    }

    /**
     * {@inheritdoc}
     */
    protected function getPackageBasePath(PackageInterface $package)
    {
        return $this->getInstallPath($package);
    }

    /**
     * {@inheritdoc}
     */
    protected function installCode(PackageInterface $package)
    {
        $package = AssetPlugin::addMainFiles($this->config, $package);

        parent::installCode($package);

        $this->deleteIgnoredFiles($package);
    }

    /**
     * {@inheritdoc}
     */
    protected function updateCode(PackageInterface $initial, PackageInterface $target)
    {
        $target = AssetPlugin::addMainFiles($this->config, $target);

        parent::updateCode($initial, $target);

        $this->deleteIgnoredFiles($target);
    }

    /**
     * Deletes files defined in bower.json in section "ignore".
     *
     * @param PackageInterface $package
     */
    protected function deleteIgnoredFiles(PackageInterface $package)
    {
        $manager = IgnoreFactory::create($this->config, $this->composer, $package, $this->getInstallPath($package));

        if ($manager->isEnabled() && !$manager->hasPattern()) {
            $this->addIgnorePatterns($manager, $package);
        }

        $manager->cleanup();
    }

    /**
     * Add ignore patterns in the manager.
     *
     * @param IgnoreManager    $manager The ignore manager instance
     * @param PackageInterface $package The package instance
     */
    protected function addIgnorePatterns(IgnoreManager $manager, PackageInterface $package)
    {
        // override this method
    }
}
