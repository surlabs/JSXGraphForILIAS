<?php
declare(strict_types=1);

/**
 * Class ilJSXGraphPluginGUI
 * @authors Saúl Díaz <info@surlabs.es>
 * @ilCtrl_isCalledBy ilJSXGraphPluginGUI: ilPCPluggedGUI
 * @ilCtrl_Calls      ilJSXGraphPluginGUI: ilObjRootFolderGUI
 */
class ilJSXGraphPluginGUI extends ilPageComponentPluginGUI {
    private ilCtrl $ctrl;
    private ilGlobalTemplateInterface $tpl;
    private ilTabsGUI $tabs;

    public function __construct() {
        parent::__construct();

        global $DIC;

        $this->ctrl = $DIC->ctrl();
        $this->tpl = $DIC->ui()->mainTemplate();
        $this->tabs = $DIC->tabs();
    }

    public function executeCommand(): void {
        $cmd = $this->ctrl->getCmd();

        if (in_array($cmd, array('create', 'save', 'edit', 'update', 'cancel'))) {
            $this->$cmd();
        }
    }

    /**
     * @throws ilCtrlException
     * @throws ilTemplateException
     */
    public function insert(): void {
        $form = $this->initForm(true);

        $this->tpl->setContent($form->getHTML());
    }

    /**
     * @throws ilCtrlException
     * @throws ilTemplateException
     */
    public function create(): void {
        $form = $this->initForm(true);

        if ($form->checkInput()) {
            $properties = array(
                'jsxcode' => $form->getInput('jsxcode'),
                'graphbox' => $form->getInput('graphbox'),
                'width' => $form->getInput('width'),
                'height' => $form->getInput('height'),
            );

            if ($this->createElement($properties)) {
                $this->tpl->setOnScreenMessage('success', $this->lng->txt('msg_obj_modified'), true);
                $this->returnToParent();
            }
        }

        $form->setValuesByPost();
        $this->tpl->setContent($form->getHtml());
    }


    /**
     * @throws ilCtrlException
     * @throws ilTemplateException
     */
    public function edit(): void {
        $this->setTabs('edit');

        $form = $this->initForm();
        $this->tpl->setContent($form->getHTML());
    }

    /**
     * @throws ilCtrlException
     * @throws ilTemplateException
     */
    public function update(): void {
        $form = $this->initForm(true);
        if ($form->checkInput()) {
            $properties = array(
                'jsxcode' => $form->getInput('jsxcode'),
                'graphbox' => $form->getInput('graphbox'),
                'width' => $form->getInput('width'),
                'height' => $form->getInput('height'),
            );

            if ($this->updateElement($properties)) {
                $this->tpl->setOnScreenMessage('success', $this->lng->txt('msg_obj_modified'), true);
                $this->returnToParent();
            }
        }

        $form->setValuesByPost();
        $this->tpl->setContent($form->getHtml());
    }

    /**
     * @throws ilCtrlException
     * @throws ilTemplateException
     */
    public function initForm($a_create = false): ilPropertyFormGUI {
        $form = new ilPropertyFormGUI();

        $v2 = new ilTextInputGUI($this->getPlugin()->txt('width'), 'width');
        $v2->setMaxLength(40);
        $v2->setSize(40);
        $form->addItem($v2);

        $v3 = new ilTextInputGUI($this->getPlugin()->txt('height'), 'height');
        $v3->setMaxLength(40);
        $v3->setSize(40);
        $form->addItem($v3);

        $pl = $this->getPlugin();
        $edittpl = $pl->getTemplate('tpl.editor.html');
        $edittpl->setVariable('BASEDIR', $pl->getDirectory());
        $edittpl->setVariable('TXT_RUN_CODE', $pl->txt('runcode'));

        if (!$a_create) {
            $prop = $this->getProperties();
            $edittpl->setVariable('GRAPHBOX', $prop['graphbox']);
            $edittpl->setVariable('JSXCODE', str_replace('"', '&quot;', $prop['jsxcode']));
            $edittpl->setVariable('HEIGHT', $prop['height']);
            $edittpl->setVariable('WIDTH', $prop['width']);
            $uniqid = $prop ['graphbox'];
        } else {
            $uniqid = uniqid('jsxgraphbox');
            $edittpl->setVariable('GRAPHBOX', $uniqid);
            $edittpl->setVariable('JSXCODE', "var brd = JXG.JSXGraph.initBoard('".$uniqid."', {boundingbox: [-2, 2, 2, -2]});");
            $edittpl->setVariable('HEIGHT', '500');
            $edittpl->setVariable('WIDTH', '500');
        }

        $jsxID = new ilNonEditableValueGUI($this->getPlugin()->txt('jsxID'), 'jsxID');
        $jsxID->setValue($uniqid);
        $jsxID->setInfo($this->getPlugin()->txt('jsxID_info'));
        $form->addItem($jsxID);

        $acehtml = $edittpl->get();
        $v1 = new ilCustomInputGUI($this->getPlugin()->txt('jsxpreview'));
        $v1->setHTML($acehtml);
        $v1->setInfo($this->getPlugin()->txt('jsxcode_info'));
        $form->addItem($v1);

        if (!$a_create) {
            $prop = $this->getProperties();
            $v2->setValue($prop['width']);
            $v3->setValue($prop['height']);
        } else {
            $v2->setValue(500);
            $v3->setValue(500);
        }

        if ($a_create) {
            $this->addCreationButton($form);
            $form->addCommandButton('cancel', $this->lng->txt('cancel'));
            $form->setTitle($this->getPlugin()->txt('cmd_insert'));
        } else {
            $form->addCommandButton('update', $this->lng->txt('save'));
            $form->addCommandButton('cancel', $this->lng->txt('cancel'));
            $form->setTitle($this->getPlugin()->txt('edit_ex_el'));
        }

        $form->setFormAction($this->ctrl->getFormAction($this));

        return $form;
    }

    public function cancel(): void {
        $this->returnToParent();
    }

    /**
     * @throws ilTemplateException
     */
    public function getElementHTML($a_mode, array $a_properties, $plugin_version): string {
        $pl = $this->getPlugin();
        $tpl = $pl->getTemplate('tpl.content.html');
        $tpl->setVariable('JSXCODE', html_entity_decode($a_properties['jsxcode']));
        $tpl->setVariable('HEIGHT', $a_properties['height']);
        $tpl->setVariable('WIDTH', $a_properties['width']);
        $tpl->setVariable('GRAPHBOX', $a_properties['graphbox']);
        $tpl->setVariable('BASEDIR', $pl->getDirectory());

        return $tpl->get();
    }

    /**
     * @throws ilCtrlException
     */
    public function setTabs($a_active): void {
        $pl = $this->getPlugin();

        $this->tabs->addTab('edit', $pl->txt('settings_1'), $this->ctrl->getLinkTarget($this, 'edit'));

        $this->tabs->activateTab($a_active);
    }
}
