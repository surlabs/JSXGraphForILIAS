<?php
declare(strict_types=1);

use ILIAS\UI\Component\Input\Container\Form\Standard;
use ILIAS\UI\Factory;
use public\Customizing\global\plugins\Services\COPage\PageComponent\JSXGraph\classes\Component\Input\Field\CustomFactory;

/**
 * Class ilJSXGraphPluginGUI
 * @authors Saúl Díaz <info@surlabs.es>
 * @ilCtrl_isCalledBy ilJSXGraphPluginGUI: ilPCPluggedGUI
 * @ilCtrl_Calls      ilJSXGraphPluginGUI: ilObjRootFolderGUI
 */
class ilJSXGraphPluginGUI extends ilPageComponentPluginGUI {
    private ilCtrl $ctrl;
    private ilGlobalTemplateInterface $tpl;
    private Factory $factory;
    private CustomFactory $customFactory;
    private \ILIAS\UI\Renderer $renderer;
    private $request;


    public function __construct() {
        parent::__construct();

        global $DIC;

        $this->ctrl = $DIC->ctrl();
        $this->tpl = $DIC->ui()->mainTemplate();
        $this->factory = $DIC->ui()->factory();
        $this->customFactory = new CustomFactory();
        $this->renderer = $DIC->ui()->renderer();
        $this->request = $DIC->http()->request();
    }

    public function executeCommand(): void {
        $cmd = $this->ctrl->getCmd();

        if (in_array($cmd, array('create', 'edit'))) {
            $this->$cmd();
        }
    }

    /**
     * @throws ilCtrlException
     */
    public function insert(): void {
        $this->ctrl->redirect($this, 'create');
    }

    /**
     * @throws ilCtrlException
     */
    public function create(): void {
        $this->tpl->setContent($this->renderForm());
    }

    /**
     * @throws ilCtrlException
     */
    public function edit(): void {
        $this->setTabs('edit');

        $this->tpl->setContent($this->renderForm());
    }

    /**
     * @throws ilCtrlException
     */
    public function renderForm(): string
    {
        $form = $this->buildForm();

        if ($this->request->getMethod() == "POST") {
            $form = $form->withRequest($this->request);
            $result = $form->getData();

            if ($result) {
                $this->save($result);
                $form = $this->buildForm();
            }
        }

        return $this->renderer->render($form);
    }

    /**
     * @throws ilCtrlException
     */
    private function buildForm(): Standard
    {
        $plugin = $this->getPlugin();

        $prop = $this->getProperties();

        $uniqid = $prop['jsxID'] ?? uniqid('jsxgraphbox');
        $jsxcode = $prop["jsxcode"] ?? "var brd = JXG.JSXGraph.initBoard('" . $uniqid . "', {boundingbox: [-2, 2, 2, -2]});";

        $inputs = [
            "width" => $this->factory->input()->field()->text($plugin->txt("width"))->withValue($prop["width"] ?? "500"),
            "height" => $this->factory->input()->field()->text($plugin->txt("height"))->withValue($prop["height"] ?? "500"),
            "jsxID" => $this->factory->input()->field()->text($plugin->txt("jsxID"), $plugin->txt("jsxID_info"))->withValue($uniqid)->withAdditionalOnLoadCode(function ($id) {
                return "$('#$id input').attr('readonly', 'readonly').css('outline', 'none');";
            }),
            "jsxcode" => $this->customFactory->jsxCode($uniqid, $plugin->txt("jsxcode"), $plugin->txt("jsxcode_info"))->withValue(str_replace('"', '&quot;', $jsxcode)),
        ];

        return $this->factory->input()->container()->form()->standard(
            $this->ctrl->getLinkTargetByClass(self::class, $this->ctrl->getCmd()),
            $inputs
        );
    }

    private function save(array $result): void
    {
        if (!empty($result['jsxcode']) && !empty($result['jsxID']) && !empty($result['width']) && !empty($result['height'])) {
            $properties = array(
                'jsxcode' => $result['jsxcode'],
                'jsxID' => $result['jsxID'],
                'width' => $result['width'],
                'height' => $result['height'],
            );

            if ((isset($this->getPCGUI()->content_obj) && $this->updateElement($properties)) || $this->createElement($properties)) {
                $this->tpl->setOnScreenMessage('success', $this->lng->txt('msg_obj_modified'), true);
                $this->returnToParent();
            }
        }
    }

    public function cancel(): void {
        $this->returnToParent();
    }

    /**
     * @throws ilTemplateException
     */
    public function getElementHTML($a_mode, array $a_properties, $plugin_version): string {
        global $DIC;

        $DIC->ui()->mainTemplate()->addCss('./Customizing/global/plugins/Services/COPage/PageComponent/JSXGraph/templates/css/jsxgraph.css');

        $pl = $this->getPlugin();
        $tpl = $pl->getTemplate('tpl.content.html');
        $tpl->setVariable('JSXCODE', html_entity_decode($a_properties['jsxcode']));
        $tpl->setVariable('HEIGHT', $a_properties['height']);
        $tpl->setVariable('WIDTH', $a_properties['width']);
        $tpl->setVariable('JSXID', $a_properties['jsxID'] ?? $a_properties['graphbox']);
        $tpl->setVariable('BASEDIR', "Customizing/global/plugins/Services/COPage/PageComponent/JSXGraph");

        return $tpl->get();
    }

    /**
     * @throws ilCtrlException
     */
    public function setTabs($a_active): void {
        global $DIC;

        $DIC->tabs()->addTab('edit', $this->lng->txt('settings'), $this->ctrl->getLinkTarget($this, 'edit'));

        $DIC->tabs()->activateTab($a_active);
    }
}