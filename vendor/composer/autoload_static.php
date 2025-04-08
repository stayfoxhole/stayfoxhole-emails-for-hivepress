<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit20586a5b78ac3040abb2b9e78215924a
{
    public static $prefixLengthsPsr4 = array (
        'F' => 
        array (
            'FoxholeEmails\\' => 14,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'FoxholeEmails\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit20586a5b78ac3040abb2b9e78215924a::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit20586a5b78ac3040abb2b9e78215924a::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit20586a5b78ac3040abb2b9e78215924a::$classMap;

        }, null, ClassLoader::class);
    }
}
