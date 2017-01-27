<?php
/**
 * Components (core subsystems + plugins) related code.
 *
 * @package    core
 * @copyright  2013 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_totara_featured_links\lib;

class class_component extends \core_component
{
    /**
     * Returns classes from given namespace across all Totara plugins and core.
     *
     * @param string $namespace plugin namespace, this cannot include the name of the plugin, for example 'rb\display'
     * @param string $instanceof full name of class or interface that returned classes should be extending/implementing
     * @param string $component restrict results to one plugin only, for example 'totara_reportbuilder'
     * @param bool $excludeabstract exclude abstract classes
     * @return string[] list of full class names in given namespace
     */
    public static function get_namespace_classes($namespace, $instanceof = null, $component = null, $excludeabstract = true) {
        self::init();

        $interface = null;
        if ($instanceof) {
            $instanceof = ltrim($instanceof, '\\'); // Normalise the class/interface name.
            if (class_exists($instanceof, true)) {
                $interface = false;
            } else if (interface_exists($instanceof, true)) {
                $interface = true;
            } else {
                debugging('Invalid $instanceof parameter, it must be a name of class or interface: ' . $instanceof, DEBUG_DEVELOPER);
                return array();
            }
        }

        if ($component) {
            $match = '/^' . preg_quote($component) .'\\\\' . preg_quote($namespace) . '\\\\[^\\\\]+$/';
            $quickmatch = $component . '\\' . $namespace . '\\';
        } else {
            $match = '/^[^\\\\]+\\\\' . preg_quote($namespace) . '\\\\[^\\\\]+$/';
            $quickmatch = '\\' . $namespace . '\\';
        }

        $classes = array();
        foreach (self::$classmap as $class => $unused) {
            if (strpos($class, $quickmatch) === false) {
                // There are very many classes in Totara, this should be faster than regrex
                // because only a small portion of classes is returned here.
                continue;
            }
            if (!preg_match($match, $class)) {
                continue;
            }
            if (!class_exists($class, true)) {
                // Most likely an interface.
                continue;
            }
            $rc = new \ReflectionClass($class);
            if ($excludeabstract and $rc->isAbstract()) {
                // This is intended to exclude base classes and helpers.
                continue;
            }
            if ($instanceof) {
                if ($instanceof === $class) {
                    // Exact match is fine, base classes are filtered out by $excludeabstract.
                } else if ($interface) {
                    if (!$rc->implementsInterface($instanceof)) {
                        continue;
                    }
                } else {
                    if (!$rc->isSubclassOf($instanceof)) {
                        continue;
                    }
                }
            }
            $classes[] = $class;
        }

        return $classes;
    }
}