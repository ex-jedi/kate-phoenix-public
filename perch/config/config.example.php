<?php
    define('PERCH_LICENSE_KEY', '');
    $http_host = getenv('HTTP_HOST');

    define("PERCH_DB_PREFIX", "");

    define('PERCH_TZ', 'Europe/London');

    define('PERCH_EMAIL_FROM', '');
    define('PERCH_EMAIL_FROM_NAME', '');

    define('PERCH_LOGINPATH', '/perch');
    define('PERCH_PATH', str_replace(DIRECTORY_SEPARATOR.'config', '', __DIR__));
    define('PERCH_CORE', PERCH_PATH.DIRECTORY_SEPARATOR.'core');

    define('PERCH_RESFILEPATH', PERCH_PATH . DIRECTORY_SEPARATOR . 'resources');
    define('PERCH_RESPATH', PERCH_LOGINPATH . '/resources');

    define('PERCH_HTML5', true);
    define(' PERCH_PRODUCTION_MODE', 'PERCH_PRODUCTION');

    define('PERCH_CUSTOM_EDITOR_CONFIGS', true);

    switch($http_host)
{
    case('relative-paths') :
      define("PERCH_DB_USERNAME", '');
      define("PERCH_DB_PASSWORD", '');
      define("PERCH_DB_SERVER", "localhost");
      define("PERCH_DB_DATABASE", "");
          define('PERCH_DEBUG', true);
        break;

    case('dev.relativepaths.uk') :
      define("PERCH_DB_USERNAME", '');
      define("PERCH_DB_PASSWORD", '');
      define("PERCH_DB_SERVER", "localhost");
      define("PERCH_DB_DATABASE", "");
        break;

    default :
        define("PERCH_DB_USERNAME", 'mysite_user');
        define("PERCH_DB_PASSWORD", 'mysite_password');
        define("PERCH_DB_SERVER", "localhost");
        define("PERCH_DB_DATABASE", "db-mysite");
        break;
    }

  ?>
