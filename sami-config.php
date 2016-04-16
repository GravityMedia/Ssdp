<?php
/**
 * This file is part of the Ssdp project.
 *
 * @author Daniel Schröder <daniel.schroeder@gravitymedia.de>
 */

/**
 * Initialize autoloader
 */
require_once __DIR__ . '/vendor/autoload.php';

/**
 * Import classes
 */
use Sami\Sami;
use Symfony\Component\Finder\Finder;

/**
 * Return sami object
 */
return new Sami(
    Finder::create()->files()->name('*.php')->in(__DIR__ . '/src'),
    [
        'title' => 'Ssdp API',
        'build_dir' => __DIR__ . '/build/docs',
        'cache_dir' => __DIR__ . '/cache/sami',
        'default_opened_level' => 2,
    ]
);
