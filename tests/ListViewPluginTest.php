<?php

namespace Spiggle\ListView\Tests;

use PHPUnit\Framework\TestCase;
use Spiggle\ListView\Filament\ListViewPlugin;
use Spiggle\ListView\ListViewServiceProvider;
use Spiggle\ListView\Support\ListAvatar;
use Spiggle\ListView\Support\ListView;

class ListViewPluginTest extends TestCase
{
    public function test_plugin_id(): void
    {
        $this->assertSame('spiggle-list-view', (new ListViewPlugin)->getId());
    }

    public function test_css_exists(): void
    {
        $path = ListViewServiceProvider::cssPath();
        $this->assertFileExists($path);
        $css = (string) file_get_contents($path);
        $this->assertStringContainsString('.slv-is-list', $css);
        $this->assertStringContainsString('.slv-toggle', $css);
        $this->assertStringContainsString('.fi-ta-group-heading', $css);
    }

    public function test_letter_and_avatar_helpers(): void
    {
        $this->assertSame('A', ListView::letter('Alice'));
        $this->assertSame('#', ListView::letter('12 orders'));
        $this->assertSame('AU', ListAvatar::initials('Admin User'));
        $this->assertSame('AL', ListAvatar::initials('Ada Lovelace'));
        $this->assertSame('AD', ListAvatar::initials('admin@example.com'));
        $this->assertStringStartsWith('data:image/svg+xml;base64,', ListAvatar::dataUri('Ada Lovelace'));
    }
}
