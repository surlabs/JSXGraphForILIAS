<?php

declare(strict_types=1);

namespace public\Customizing\global\plugins\Services\COPage\PageComponent\JSXGraph\classes\Component\Input\Field;

use ILIAS\UI\Component\Component;
use ILIAS\UI\Component\Input\Container\Form\FormInput;
use ILIAS\UI\Implementation\Component\Input\Field\Renderer as RendererILIAS;
use ILIAS\UI\Implementation\Render\Template;
use ilJSXGraphPlugin;
use ilRTE;
use ilTemplate;
use ilTemplateException;
use ilTinyMCE;

/**
 * Class Renderer
 */
class Renderer extends RendererILIAS
{
    private \ILIAS\UI\Renderer $default_renderer;

    /**
     * @throws ilTemplateException
     */
    public function render(Component $component, \ILIAS\UI\Renderer $default_renderer): string
    {
        global $DIC;

        $this->default_renderer = $default_renderer;

        return match (true) {
            $component instanceof JSXCode => $this->renderJSXCode($component),
            default => $this->default_renderer->render($component),
        };
    }

    /**
     * @throws ilTemplateException
     */
    protected function wrapInFormContext(
        FormInput $component,
        string $label,
        string $input_html,
        ?string $id_for_label = null,
        ?string $dependant_group_html = null
    ): string {
        $tpl = new ilTemplate("Input/tpl.context_form.html", true, true, 'components/ILIAS/UI/src');

        $tpl->setVariable("LABEL", $label);
        $tpl->setVariable("INPUT", $input_html);
        $tpl->setVariable("UI_COMPONENT_NAME", $this->getComponentCanonicalNameAttribute($component));
        $tpl->setVariable("INPUT_NAME", $component->getName());

        if ($component->getOnLoadCode() !== null) {
            $binding_id = $this->bindJavaScript($component) ?? $this->createId();
            $tpl->setVariable("BINDING_ID", $binding_id);
        }

        if ($id_for_label) {
            $tpl->setCurrentBlock('for');
            $tpl->setVariable("ID", $id_for_label);
            $tpl->parseCurrentBlock();
        } else {
            $tpl->touchBlock('tabindex');
        }

        $byline = $component->getByline();
        if ($byline) {
            $tpl->setVariable("BYLINE", $byline);
        }

        $required = $component->isRequired();
        if ($required) {
            $tpl->setCurrentBlock('required');
            $tpl->setVariable("REQUIRED_ARIA", $this->txt('required_field'));
            $tpl->parseCurrentBlock();
        }

        if ($component->isDisabled()) {
            $tpl->touchBlock("disabled");
        }

        $error = $component->getError();
        if ($error) {
            $error_id = $this->createId();
            $tpl->setVariable("ERROR_LABEL", $this->txt("ui_error"));
            $tpl->setVariable("ERROR_ID", $error_id);
            $tpl->setVariable("ERROR", $error);
            if ($id_for_label) {
                $tpl->setVariable("ERROR_FOR_ID", $id_for_label);
            }
        }

        if ($dependant_group_html) {
            $tpl->setVariable("DEPENDANT_GROUP", $dependant_group_html);
        }
        return $tpl->get();
    }

    protected function maybeDisable(FormInput $component, ilTemplate|Template $tpl): void
    {
        if ($component->isDisabled()) {
            $tpl->setVariable("DISABLED", 'disabled="disabled"');
        }
    }

    protected function applyName(FormInput $component, ilTemplate|Template $tpl): ?string
    {
        $name = $component->getName();
        $tpl->setVariable("NAME", $name);
        return $name;
    }

    protected function applyValue(FormInput $component, ilTemplate|Template $tpl, callable $escape = null): void
    {
        $value = $component->getValue();
        if (!is_null($escape)) {
            $value = $escape($value);
        }
        if (isset($value) && $value != '') {
            $tpl->setVariable("VALUE", $this->_solveKeyBracketsBug($value));
        }
    }

    public function _solveKeyBracketsBug($text): array|string
    {
        $text1 = str_replace("{", "&#123;", $text);

        return str_replace("}", "&#125;", $text1);
    }

    private function getTemplateCustom(string $name): ilTemplate
    {
        return new ilTemplate("Component/Input/Field/$name", true, true, 'public/Customizing/global/plugins/Services/COPage/PageComponent/JSXGraph');
    }

    /**
     * @throws ilTemplateException
     */
    private function renderJSXCode(JSXCode $component): string
    {
        /** @var $component JSXCode */
        $component = $component->withAdditionalOnLoadCode(
            static function ($id): string {
                return "
                    il.UI.Input.textarea.init('$id');
                ";
            }
        );

        $tpl = $this->getTemplateCustom("tpl.JSXCode.html");

        if (0 < $component->getMaxLimit()) {
            $tpl->setVariable('REMAINDER_TEXT', $this->txt('ui_chars_remaining'));
            $tpl->setVariable('REMAINDER', $component->getMaxLimit() - strlen($component->getValue() ?? ''));
            $tpl->setVariable('MAX_LIMIT', $component->getMaxLimit());
        }

        if (null !== $component->getMinLimit()) {
            $tpl->setVariable('MIN_LIMIT', $component->getMinLimit());
        }

        $this->applyName($component, $tpl);
        $this->applyValue($component, $tpl, $this->htmlEntities());
        $this->maybeDisable($component, $tpl);

        /** JSXGraph */
        $plugin = ilJSXGraphPlugin::getInstance();

        $tpl->setVariable('BASEDIR', "Customizing/global/plugins/Services/COPage/PageComponent/JSXGraph");
        $tpl->setVariable('JSXID', $component->getJsxID());
        $tpl->setVariable('TXT_RUN_CODE', $plugin->txt('runcode'));

        return $this->wrapInFormContext($component, $component->getLabel(), $tpl->get());
    }
}
