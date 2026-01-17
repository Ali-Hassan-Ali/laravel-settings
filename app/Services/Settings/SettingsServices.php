<?php

namespace App\Services\Settings;

use App\Models\Setting;
use ArrayAccess;

class SettingsServices implements ArrayAccess
{
    private string $key;
    private mixed $value;
    private mixed $lang;

    public function __construct(?string $key, ?string $lang)
    {
        $this->key = $key;
        $this->lang = $lang ?? app()->getLocale();
        $this->loadValue();
    }

    public function save(array | string $data): void
    {
        $value = is_array($data) ? json_encode($data) : $data;

        Setting::updateOrCreate(['key' => $this->key], ['value' => $value]);

        $this->value = is_array($data) ? $data : json_decode($value, true);
    }

    private function loadValue(): void
    {
        $setting = Setting::where('key', $this->key)->first();
        
        $this->value = isset($setting?->value) ? (json_validate($setting?->value) ? json_decode($setting->value, true) : $setting?->value) : null;
    }

    public function get(): mixed
	{
        if (!is_array($this->value)) return [];
   
        return array_map(fn($item) => (object) collect($item)->mapWithKeys(
            fn($value, $key) => [$key => is_array($value) ? ($value[$this->lang] ?? reset($value)) : $value]
        )->all(), $this->value ?? []);
	}

    public function each(callable $callback): self
    {
        $items = is_array($this->value) ? $this->get() : [];

        foreach ($items as $key => $item) {
            
            $callback($item, $key);
        }

        return $this;
    }

    public function __get($property)
    {
        if(empty($this->value[$property][$this->lang]) && empty($this->value[$property])) return null;
        
        return $this->value[$property][$this->lang] ?? $this->value[$property] ?? $this->value ?? null;
    }

    public function toArray(): mixed
    { 
        return $this->value ?? [];
    }

    public function offsetExists($offset): bool
    {
        return isset($this->value[$offset]);
    }

    public function offsetGet($offset): mixed
    {
        return $this->__get($offset);
    }

    public function offsetSet($offset, $value): void {}
    
    public function offsetUnset($offset): void {}
}