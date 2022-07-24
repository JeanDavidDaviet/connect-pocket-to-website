<?php

namespace JDD\CPTW;

trait CPTWTrait {
    /**
     * This plugin's version number. Used for busting caches.
     *
     * @var string
     */
    public $version = '1.0.1';

    /**
     * This plugin's unique slug
     *
     * @var string
     */
    public $slug = 'connect-pocket-to-website';

    /**
     * This plugin's prefix
     *
     * @var string
     */
    private $prefix = 'cptw_';

    /**
     * The default admin page url.
     *
     * @var string
     */
    public $admin_url = '';

    /**
     * The capability required to access this plugin's settings.
     *
     * @var string
     */
    public $capability_settings = 'manage_options';
}