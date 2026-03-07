<?php

declare(strict_types=1);

namespace BNT;

use BNT\Exception\WarningException;
use BNT\Language;
use BNT\Translate;

class Fetch
{

    protected Language $language;
    protected array $data;
    protected string $path;
    protected bool $isRequired = false;
    protected string $requiredMessage = ':label is required';
    protected bool $notEmpty = false;
    protected string $notEmptyMessage = ':label cannot be empty';
    protected string $filterMessage = ':label is invalid';
    protected string $enumMessage = ':label contains is not allow value';
    protected ?string $label = null;
    protected array $typeMessages = [];
    protected ?int $filter = null;
    protected bool $trim = false;
    protected mixed $default = null;
    protected ?array $enum = null;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function language(Language $language): void
    {
        $this->language = $language;
    }

    public function filter(int $filter): self
    {
        $this->filter = $filter;

        return $this;
    }

    public function default(mixed $default): self
    {
        $this->default = $default;

        return $this;
    }

    public function enum(array $enum): self
    {
        $this->enum = $enum;

        return $this;
    }

    public function enumMessage(string $message): self
    {
        $this->enumMessage = $message;

        return $this;
    }

    public function trim(): self
    {
        $this->trim = true;

        return $this;
    }

    public function path(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function required(): self
    {
        $this->isRequired = true;

        return $this;
    }

    public function requiredMessage(string $message): self
    {
        $this->requiredMessage = $message;

        return $this;
    }

    public function notEmpty(): self
    {
        $this->notEmpty = true;

        return $this;
    }

    public function notEmptyMessage(string $message): self
    {
        $this->notEmptyMessage = $message;

        return $this;
    }

    public function filterMessage(string $message): self
    {
        $this->filterMessage = $message;

        return $this;
    }

    protected function t(array|string $tag, array $replace = [], ?string $format = null): string
    {
        $translate = new Translate;
        $translate->language($this->language);
        $translate->translate($tag, $replace, $format);

        return (string) $translate;
    }

    public function label(string $label): self
    {
        $this->label = $this->t($label);

        return $this;
    }

    public function typeMessage(string $type, string $message): self
    {
        $this->typeMessages[$type] = $message;

        return $this;
    }

    protected function formatMessage(string $message): string
    {
        return str_replace(':label', $this->label ?? $this->path, $message);
    }

    protected function val(): mixed
    {
        if ($this->path === null) {
            throw new WarningException("Path must be set before calling as* methods");
        }

        $val = $this->getValueFromPath($this->path);

        if (isset($this->default) && is_null($val)) {
            $val = $this->default;
        }

        if (is_string($val) && $this->trim) {
            $val = trim($val);
        }

        if (isset($this->enum) && !in_array($val, $this->enum, true)) {
            throw new WarningException($this->formatMessage($this->enumMessage));
        }

        if ($this->isRequired && $val === null) {
            throw new WarningException($this->formatMessage($this->requiredMessage));
        }

        if ($this->notEmpty && ($val === null || $val === '')) {
            throw new WarningException($this->formatMessage($this->notEmptyMessage));
        }

        if ($this->filter) {
            $filterVal = filter_var($val, $this->filter, FILTER_NULL_ON_FAILURE);

            if ($filterVal === null) {
                throw new WarningException($this->formatMessage($this->filterMessage));
            }
        }

        return $val;
    }

    public function asString(): string
    {
        $value = $this->val();

        if ($value === null) {
            return '';
        }

        if (!is_scalar($value) && !(is_object($value) && method_exists($value, '__toString'))) {
            $message = $this->typeMessages['string'] ?? 'Cannot convert :label to string';
            throw new WarningException($this->formatMessage($message));
        }

        return (string) $value;
    }

    public function asInt(): int
    {
        $value = $this->val();

        if ($value === null) {
            return 0;
        }

        if (!is_numeric($value)) {
            $message = $this->typeMessages['int'] ?? 'Cannot convert :label to integer';
            throw new WarningException($this->formatMessage($message));
        }

        $intValue = (int) $value;
        if ((string) $intValue !== (string) $value && $value !== $intValue) {
            $message = $this->typeMessages['int'] ?? 'Cannot convert :label to integer';
            throw new WarningException($this->formatMessage($message));
        }

        return $intValue;
    }

    public function asFloat(): float
    {
        $value = $this->val();

        if ($value === null) {
            return 0.0;
        }

        if (!is_numeric($value)) {
            $message = $this->typeMessages['float'] ?? 'Cannot convert :label to float';
            throw new WarningException($this->formatMessage($message));
        }

        return (float) $value;
    }

    public function asBool(): bool
    {
        $value = $this->val();

        if ($value === null) {
            return false;
        }

        if (is_bool($value)) {
            return $value;
        }

        if (is_string($value)) {
            $lower = strtolower($value);
            if ($lower === 'true' || $lower === '1' || $lower === 'on' || $lower === 'yes') {
                return true;
            }
            if ($lower === 'false' || $lower === '0' || $lower === 'off' || $lower === 'no' || $lower === '') {
                return false;
            }
        }

        if (is_numeric($value)) {
            return (bool) $value;
        }

        $message = $this->typeMessages['bool'] ?? 'Cannot convert :label to boolean';
        throw new WarningException($this->formatMessage($message));
    }

    public function asEnum(string $enumClass)
    {
        $value = $this->val();

        if ($value === null) {
            return null;
        }

        if (!enum_exists($enumClass)) {
            $message = $this->typeMessages['enum'] ?? "Enum class {$enumClass} does not exist";
            throw new WarningException($this->formatMessage($message));
        }

        $enum = $enumClass::tryFrom($value);

        if ($enum === null) {
            $message = $this->typeMessages['enum'] ?? "Cannot convert :label to enum {$enumClass}";
            throw new WarningException($this->formatMessage($message));
        }

        return $enum;
    }

    public function asDateTime(): ?\DateTimeInterface
    {
        $value = $this->val();

        if ($value === null) {
            return null;
        }

        try {
            if ($value instanceof \DateTimeInterface) {
                return $value;
            }

            if (is_numeric($value)) {
                $datetime = new \DateTime();
                $datetime->setTimestamp((int) $value);
                return $datetime;
            }

            return new \DateTime($value);
        } catch (\Exception $e) {
            $message = $this->typeMessages['datetime'] ?? 'Cannot convert :label to DateTime';
            throw new WarningException($this->formatMessage($message));
        }
    }

    protected function getValueFromPath(string $path): mixed
    {
        $keys = explode('.', $path);
        $current = $this->data;

        foreach ($keys as $key) {
            if (is_array($current) && array_key_exists($key, $current)) {
                $current = $current[$key];
            } elseif (is_object($current) && property_exists($current, $key)) {
                $current = $current->{$key};
            } else {
                return null;
            }
        }

        return $current;
    }
}
