<?php

declare(strict_types=1);

namespace public\Customizing\global\plugins\Services\COPage\PageComponent\JSXGraph\classes\Component\Input\Field;

use Closure;
use ILIAS\Data\Factory;
use ILIAS\Refinery\Constraint;
use ILIAS\UI\Component\Input\Field\Textarea;
use ILIAS\UI\Component\Signal;
use ILIAS\UI\Implementation\Component\Input\Input;
use ILIAS\UI\Implementation\Component\JavaScriptBindable;
use ILIAS\UI\Implementation\Component\Triggerer;


/**
 * Class JSXCode
 */
class JSXCode extends Input implements Textarea {
    use JavaScriptBindable;
    use Triggerer;

    public string $jsxID;
    protected string $label;
    protected ?string $byline;
    protected bool $is_required = false;
    protected bool $is_disabled = false;
    protected ?Constraint $requirement_constraint = null;

    protected ?int $max_limit = null;

    protected ?int $min_limit = null;

    public function __construct(string $jsxID, string $label, ?string $byline = null)
    {
        global $DIC;

        $this->jsxID = $jsxID;
        $this->label = $label;
        $this->byline = $byline;

        parent::__construct(new Factory(), $DIC->refinery());
    }

    public function getJsxID(): string
    {
        return $this->jsxID;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @inheritdoc
     */
    public function withLabel(string $label): self
    {
        $clone = clone $this;
        $clone->label = $label;
        return $clone;
    }

    /**
     * @inheritdoc
     */
    public function getByline(): ?string
    {
        return $this->byline;
    }

    /**
     * @inheritdoc
     */
    public function withByline(string $byline): self
    {
        $clone = clone $this;
        $clone->byline = $byline;
        return $clone;
    }

    /**
     * @inheritdoc
     */
    public function isRequired(): bool
    {
        return $this->is_required;
    }

    /**
     * @inheritdoc
     */
    public function withRequired(bool $is_required, ?Constraint $requirement_constraint = null): self
    {
        $clone = clone $this;
        $clone->is_required = $is_required;
        $clone->requirement_constraint = ($is_required) ? $requirement_constraint : null;
        return $clone;
    }

    /**
     * @inheritdoc
     */
    public function isDisabled(): bool
    {
        return $this->is_disabled;
    }

    /**
     * @inheritdoc
     */
    public function withDisabled(bool $is_disabled): self
    {
        $clone = clone $this;
        $clone->is_disabled = $is_disabled;
        return $clone;
    }

    public function withOnUpdate(Signal $signal): self
    {
        return $this->withTriggeredSignal($signal, 'update');
    }

    /**
     * @inheritdoc
     */
    public function appendOnUpdate(Signal $signal): self
    {
        return $this->appendTriggeredSignal($signal, 'update');
    }

    public function withMaxLimit(int $max_limit): JSXCode
    {
        $clone = $this->withAdditionalTransformation(
            $this->refinery->string()->hasMaxLength($max_limit)
        );

        $clone->max_limit = $max_limit;

        return $clone;
    }

    /**
     * get maximum limit of characters
     * @return int|null
     */
    public function getMaxLimit(): ?int
    {
        return $this->max_limit;
    }

    /**
     * set minimum number of characters
     */
    public function withMinLimit(int $min_limit): JSXCode
    {
        $clone = $this->withAdditionalTransformation(
            $this->refinery->string()->hasMinLength($min_limit)
        );

        $clone->min_limit = $min_limit;

        return $clone;
    }

    /**
     * get minimum limit of characters
     * @return int|null
     */
    public function getMinLimit(): ?int
    {
        return $this->min_limit;
    }

    protected function isClientSideValueOk($value): bool
    {
        return is_string($value);
    }

    protected function getConstraintForRequirement(): ?Constraint
    {
        if ($this->requirement_constraint !== null) {
            return $this->requirement_constraint;
        }

        if ($this->min_limit) {
            return $this->refinery->string()->hasMinLength($this->min_limit);
        }
        return $this->refinery->string()->hasMinLength(1);
    }

    public function isLimited(): bool
    {
        return $this->min_limit > 0 || $this->max_limit > 0;
    }

    public function withoutStripTags(): Textarea
    {
        return clone $this;
    }

    public function getUpdateOnLoadCode(): Closure
    {
        return fn($id) => "$('#$id').on('input', function(event) {
				il.UI.input.onFieldUpdate(event, '$id', $('#$id').val());
			});
			il.UI.input.onFieldUpdate(event, '$id', $('#$id').val());";
    }
}
