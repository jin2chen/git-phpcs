<?php

namespace jin2chen\php\cs\test\unit;

use jin2chen\php\cs\PrePushSniffer;
use PHPUnit\Framework\TestCase;

class PrePushSnifferTest extends TestCase
{
    public function testCommandExist()
    {
        $this->assertTrue(PrePushSniffer::commandExist('php'));
        $this->assertFalse(PrePushSniffer::commandExist('php-not-exist'));
    }

    public function testFindPhpcsCommand()
    {
        $cs = new PrePushSniffer('/usr/local/bin/git', '/usr/local/bin/phpcs');
        $this->assertSame('/usr/local/bin/git', $cs->findGitCommand());
        $this->assertSame('/usr/local/bin/phpcs', $cs->findPhpcsCommand());
    }

    public function test()
    {
        $arg = 'refs/heads/master fc4dc085b5c97b6a7e3d4523333f34d63b47d383 refs/heads/master 75ef27a5f778717f752b513d15f0377c5b7cad18';
        $cs = new PrePushSniffer();
        $this->assertTrue(in_array('tests/sample/Sample.php', $cs->internalExtractFiles(...explode(' ', $arg))));
    }
}
