<?php

namespace Tests\Feature\App\Services\Settings;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class SettingsServicesTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_can_save_and_retrieve_logo_file()
    {
        Storage::fake('public');

        $oldFile = UploadedFile::fake()->image('old_logo.png');
        $firstPath = $oldFile->store('website', 'public');

        setting('website')->save([
            'logo_path' => $firstPath,
        ]);

        Storage::disk('public')->assertExists(setting('website')->logo_path);
        $this->assertEquals($firstPath, setting('website')->logo_path);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_can_save_and_retrieve_single_setting()
    {
        $items = [
            'name' => 'name value new',
            'email' => 'email value',
        ];

        setting('item_single')->save($items);

        $this->assertEquals('name value new', setting('item_single')->name);
        $this->assertEquals('email value', setting('item_single')->email);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_can_save_and_retrieve_single_language_setting()
    {
        $items = [
            'name' => ['ar' => 'name ar', 'en' => 'name en'],
            'email' => ['ar' => 'email ar', 'en' => 'email en'],
        ];

        setting('single_language')->save($items);

        $locale = app()->getLocale();

        $this->assertEquals($items['name'][$locale], setting('single_language')->name);
        $this->assertEquals($items['email'][$locale], setting('single_language')->email);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_can_save_and_retrieve_single_multiple_items()
    {
        $items = [
            ['name' => 'name value 1', 'email' => 'email value 1'],
            ['name' => 'name value 2', 'email' => 'email value 2'],
        ];

        setting('single_multiple_items')->save($items);

        setting('single_multiple_items')->each(function ($item) {
            $this->assertEquals($item->name, $item->name);
        });

        $result = setting('single_multiple_items')->get();

        foreach ($result as $index => $value) {
            $this->assertEquals($items[$index]['name'], $value->name);
            $this->assertEquals($items[$index]['email'], $value->email);
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_can_save_and_retrieve_single_multiple_language_items()
    {
        $items = [
            [
                'name' => ['ar' => 'name value 1 ar', 'en' => 'name value 1 en'],
                'email' => ['ar' => 'email value 1 ar', 'en' => 'email value 1 en'],
            ],
            [
                'name' => ['ar' => 'name value 2 ar', 'en' => 'name value 2 en'],
                'email' => ['ar' => 'email value 2 ar', 'en' => 'email value 2 en'],
            ],
        ];

        setting('single_multiple_language_items')->save($items);

        $result = setting('single_multiple_language_items')->get();
        $locale = app()->getLocale();

        foreach ($result as $index => $value) {
            $this->assertEquals($items[$index]['name'][$locale], $value->name);
            $this->assertEquals($items[$index]['email'][$locale], $value->email);
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_can_save_and_retrieve_multiple_items()
    {
        $items = [
            ['name' => 'name 1 multiples', 'email' => 'email 1 multiples'],
            ['name' => 'name 2 multiples', 'email' => 'email 2 multiples'],
        ];

        setting('multiple_items')->save($items);

        $result = setting('multiple_items')->get();

        foreach ($result as $index => $value) {
            $this->assertEquals($items[$index]['name'], $value->name);
            $this->assertEquals($items[$index]['email'], $value->email);
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function test_can_save_and_retrieve_multiple_language_items()
    {
        $items = [
            [
                'name' => ['ar' => 'name value 1 ar', 'en' => 'name value 1 en'],
                'email' => ['ar' => 'email value 1 ar', 'en' => 'email value 1 en'],
            ],
            [
                'name' => ['ar' => 'name value 2 ar', 'en' => 'name value 2 en'],
                'email' => ['ar' => 'email value 2 ar', 'en' => 'email value 2 en'],
            ],
        ];

        setting('multiple_language_items')->save($items);

        $result = setting('multiple_language_items')->get();
        $locale = app()->getLocale();

        foreach ($result as $index => $value) {
            $this->assertEquals($items[$index]['name'][$locale], $value->name);
            $this->assertEquals($items[$index]['email'][$locale], $value->email);
        }
    }
}
