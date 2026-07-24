<?php
declare(strict_types=1);

/**
 * Class ilJSXGraphPlugin
 * @authors Saúl Díaz <info@surlabs.es>
 * @ilCtrl_isCalledBy ilJSXGraphPluginGUI
 */
class ilJSXGraphPlugin extends ilPageComponentPlugin {
    private static ?self $instance = null;
    const PLUGIN_NAME = 'JSXGraph';

    public function getPluginName(): string {
        return 'JSXGraph';
    }

    public function isValidParentType(string $a_type): bool {
        return true;
    }

    public function onClone(
        array &$a_properties,
        string $a_plugin_version
    ): void {
        $newid = uniqid("jsxgraphbox");
        $a_properties["jsxcode"] = str_replace($a_properties["jsxID"], $newid, $a_properties["jsxcode"]);
        $a_properties["jsxID"] = $newid;
    }

    public static function getInstance(): self
    {
        if (!isset(self::$instance)) {
            global $DIC;

            $component_repository = $DIC["component.repository"];

            $info = null;
            $plugin_name = self::PLUGIN_NAME;
            $info = $component_repository->getPluginByName($plugin_name);

            $component_factory = $DIC["component.factory"];

            $plugin_obj = $component_factory->getPlugin($info->getId());

            self::$instance = $plugin_obj;
        }

        return self::$instance;
    }
}
