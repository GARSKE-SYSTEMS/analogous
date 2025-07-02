<?php
namespace Analogous\Util;

/**
 * ConfigHelper.php
 *
 * Provides methods to load and access configuration values from a config.ini file.
 *
 * @author Convobis Project
 */
class ConfigHelper
{

    /**
     * Loads the configuration from the config.ini file into a global variable.
     */
    public static function loadConfig()
    {
        if (isset($GLOBALS["cb_config"])) {
            return;
        }

        $configFile = __DIR__ . '/../config/config.ini';
        if (!file_exists($configFile)) {
            throw new \Exception("Configuration file not found: " . $configFile);
        }
        $config = parse_ini_file($configFile, true);
        if ($config === false) {
            throw new \Exception("Failed to parse configuration file: " . $configFile);
        }
        $GLOBALS["cb_config"] = $config;
    }

    /**
     * Get a configuration value by key, supports dot notation for nested values.
     *
     * @param string $key The configuration key, e.g. 'database.host'.
     * @param mixed $default The default value to return if the key does not exist.
     * @return mixed The configuration value or the default value if not found.
     */
    public static function getConfigValue($key, $default = null, $createIfNotExists = false)
    {
        if (!isset($GLOBALS["cb_config"])) {
            self::loadConfig(); // Load configuration if not already loaded
        }
        $keys = explode('.', $key);
        // Find the value in the global multi-dimensional configuration array by splitting the key by dots
        if (isset($GLOBALS["cb_config"][$keys[0]])) {
            $value = $GLOBALS["cb_config"][$keys[0]];
            for ($i = 1; $i < count($keys); $i++) {
                if (isset($value[$keys[$i]])) {
                    $value = $value[$keys[$i]];
                } else {
                    return $default;
                }
            }
            return $value;
        }
        if ($createIfNotExists) {
            ConfigHelper::setConfigValue($key, $default); // Create the key with default value if it does not exist
        }
        return $default;
    }

    public static function setConfigValue($key, $value)
    {
        if (!isset($GLOBALS["cb_config"])) {
            self::loadConfig(); // Load configuration if not already loaded
        }
        $keys = explode('.', $key);
        $config = &$GLOBALS["cb_config"];
        foreach ($keys as $k) {
            if (!isset($config[$k])) {
                $config[$k] = [];
            }
            $config = &$config[$k];
        }
        $config = $value; // Set the value
    }

}