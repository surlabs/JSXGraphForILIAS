<?php

declare(strict_types=1);

namespace public\Customizing\global\plugins\Services\COPage\PageComponent\JSXGraph\classes\Component\Input\Field;

/**
 * Class CustomFactory
 */
class CustomFactory
{
    public function jsxCode(string $jsxID, string $label, ?string $by_line = null): JSXCode
    {
        return new JSXCode($jsxID, $label, $by_line);
    }
}