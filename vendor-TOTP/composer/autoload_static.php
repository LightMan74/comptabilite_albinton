<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit336586eadf708946c266d05c0d7dc54a
{
    public static $prefixLengthsPsr4 = array (
        'R' => 
        array (
            'RobThree\\Auth\\' => 14,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'RobThree\\Auth\\' => 
        array (
            0 => __DIR__ . '/..' . '/robthree/twofactorauth/lib',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit336586eadf708946c266d05c0d7dc54a::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit336586eadf708946c266d05c0d7dc54a::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit336586eadf708946c266d05c0d7dc54a::$classMap;

        }, null, ClassLoader::class);
    }
}
