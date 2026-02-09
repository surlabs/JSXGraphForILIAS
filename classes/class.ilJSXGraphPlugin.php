<?php
declare(strict_types=1);

/**
 * Class ilJSXGraphPlugin
 * @authors Saúl Díaz <info@surlabs.es>
 * @ilCtrl_isCalledBy ilJSXGraphPluginGUI
 */
class ilJSXGraphPlugin extends ilPageComponentPlugin {
    public function getPluginName(): string {
        return 'JSXGraph';
    }

    public function isValidParentType(string $a_type): bool {
        return true;
    }

    public function getJavascriptFiles($a_mode): array {
        return array('templates/js/jsxgraphcore.js');
    }

    public function getCssFiles($a_mode): array {
        return array('templates/css/jsxgraph.css');
    }

    public function onClone(
        array &$a_properties,
        string $a_plugin_version
    ): void {
        $newid = uniqid("jsxgraphbox");
        $a_properties["jsxcode"] = str_replace($a_properties["graphbox"], $newid, $a_properties["jsxcode"]);
        $a_properties["graphbox"] = $newid;
    }
}
