<?php

declare(strict_types=1);

namespace BNT;

use BNT\Exception\WarningException;
use BNT\Language;
use BNT\Translate;

class Fetch
{

    use Traits\TranslateTrait;

    protected Language $language;
    protected array $data;
    protected string $path;
    protected bool $isRequired = false;
    protected bool $notEmpty = false;
    protected ?Translate $label = null;

    /**
     * @var array<string, Translate>
     */
    protected array $messageTemplates = [];
    protected ?int $filter = null;
    protected bool $trim = false;
    protected mixed $default = null;
    protected ?array $enum = null;

    public function __construct($data)
    {
        $this->messageTemplates = [
            'is_required' => $this->t('[label] is required'),
            'filter_is_invalid' => $this->t('[label] is invalid'),
            'not_empty' => $this->t('[label] cannot be empty'),
            'not_allow_value' => '[label] contains is not allow value',
            'convert_string' => $this->t('Cannot convert [label] to string'),
            'convert_int' => $this->t('Cannot convert [label] to integer'),
            'convert_datetime' => $this->t('Cannot convert [label] to DateTime'),
            'convert_float' => $this->t('Cannot convert [label] to float'),
            'convert_bool' => $this->t('Cannot convert [label] to boolean'),
            'convert_enum_not_exists' => $this->t('Enum class [enumClass] for [label] does not exist'),
            'convert_enum' => $this->t('Cannot convert [label] to enum [enumClass]'),
        ];

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

    public function trim(): self
    {
        $this->trim = true;

        return $this;
    }

    public function path(string $path): self
    {
        $this->path = $path;
        $this->label($path);

        return $this;
    }

    public function required(): self
    {
        $this->isRequired = true;

        return $this;
    }

    public function notEmpty(): self
    {
        $this->notEmpty = true;

        return $this;
    }

    public function label(string $label): self
    {
        foreach ($this->messageTemplates as $messageTemplate) {
            Translate::as($messageTemplate)->replace('label', $this->t($label));
        }

        return $this;
    }

    public function messageTemplate(string $type, Translate $message): self
    {
        $this->messageTemplates[$type] = $message;

        return $this;
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
            throw new WarningException()->tt($this->messageTemplates['not_allow_value']);
        }

        if ($this->isRequired && $val === null) {
            throw new WarningException()->tt($this->messageTemplates['is_required']);
        }

        if ($this->notEmpty && ($val === null || $val === '')) {
            throw new WarningException()->tt($this->messageTemplates['not_empty']);
        }

        if ($this->filter) {
            $filterVal = filter_var($val, $this->filter, FILTER_NULL_ON_FAILURE);

            if ($filterVal === null) {
                throw new WarningException()->tt($this->messageTemplates['filter_is_invalid']);
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
            throw new WarningException()->tt($this->messageTemplates['convert_string']);
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
            throw new WarningException()->tt($this->messageTemplates['convert_int']);
        }

        $intValue = (int) $value;
        if ((string) $intValue !== (string) $value && $value !== $intValue) {
            throw new WarningException()->tt($this->messageTemplates['convert_int']);
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
            throw new WarningException()->tt($this->messageTemplates['convert_float']);
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

        throw new WarningException()->tt($this->messageTemplates['convert_bool']);
    }

    public function asEnum(string $enumClass)
    {
        $value = $this->val();

        if ($value === null) {
            return null;
        }

        if (!enum_exists($enumClass)) {
            throw new WarningException()->tt(Translate::as($this->messageTemplates['convert_enum_not_exists'])->replace('enumClass', $enumClass));
        }

        $enum = $enumClass::tryFrom($value);

        if ($enum === null) {
            throw new WarningException()->tt(Translate::as($this->messageTemplates['convert_enum'])->replace('enumClass', $enumClass));
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
            throw new WarningException()->tt($this->messageTemplates['convert_datetime']);
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
