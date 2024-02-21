<?php
/**
 * Simple autoloader, so we don't need Composer just for this.
 */
    class Autoloader
    {
        public static function register()
        {
            spl_autoload_register(function ($class) {
                $file = str_replace('\\', DIRECTORY_SEPARATOR, $class).'.php';
                if (file_exists($file)) {
                    require $file;
                    return true;
                }
                return false;
            });
        } //include throws warning, require throws error
    }
    Autoloader::register();
//driver code inside the index.php, no other files in root
//implement .htaccess, so that the user can only access the root directory index.php
?>

