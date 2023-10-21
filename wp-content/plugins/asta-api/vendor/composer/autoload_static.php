<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInita54ab0ff9af0df781fa8a7334263145a
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Stripe\\' => 7,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Stripe\\' => 
        array (
            0 => __DIR__ . '/..' . '/stripe/stripe-php/lib',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInita54ab0ff9af0df781fa8a7334263145a::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInita54ab0ff9af0df781fa8a7334263145a::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInita54ab0ff9af0df781fa8a7334263145a::$classMap;

        }, null, ClassLoader::class);
    }
}