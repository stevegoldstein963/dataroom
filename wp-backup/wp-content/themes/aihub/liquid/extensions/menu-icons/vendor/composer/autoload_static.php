<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitbf1297da02d6475c76ee8fa5bb3a7c73
{
    public static $files = array (
        '0498965e576e4ec1efaedeccfee8b5f0' => __DIR__ . '/..' . '/codeinwp/menu-item-custom-fields/menu-item-custom-fields.php',
        '347e48cf03d89942a6ddafc17d247b11' => __DIR__ . '/..' . '/codeinwp/icon-picker/icon-picker.php',
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInitbf1297da02d6475c76ee8fa5bb3a7c73::$classMap;

        }, null, ClassLoader::class);
    }
}