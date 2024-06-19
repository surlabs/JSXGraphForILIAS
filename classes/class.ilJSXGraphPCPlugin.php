<?php
declare(strict_types=1);

/**
 * Class ilJSXGraphPCPlugin
 * @authors Saúl Díaz <info@surlabs.es>
 * @ilCtrl_isCalledBy ilJSXGraphPCPluginGUI
 */
class ilJSXGraphPCPlugin extends ilPageComponentPlugin {
    public function getPluginName(): string {
        return 'JSXGraphPC';
    }

    public function isValidParentType($a_type) {
        return true;
    }

    public function getJavascriptFiles($a_mode): array {
        return array('templates/js/jsxgraphcore.js');
    }

    public function getCssFiles($a_mode): array {
        return array('templates/css/jsxgraph.css');
    }

    public function onClone(&$a_properties, $a_plugin_version) {
        $newid = uniqid("jsxgraphbox");
        $a_properties["jsxcode"] = str_replace($a_properties["graphbox"], $newid, $a_properties["jsxcode"]);
        $a_properties["graphbox"] = $newid;
    }
}
