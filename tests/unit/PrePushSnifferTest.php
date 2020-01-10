<?php

namespace mole\php\cs\test\unit;

use mole\php\cs\PrePushSniffer;
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
        $arg = 'refs/heads/master 3b2c02364d87e6377877a3f9e6829ce0c9d350a0 refs/heads/master d9546de866a1469beec61c021f0b495e05453666';
        $cs = new PrePushSniffer();
        $this->assertArraySubset(['tests/sample/Sample.php'], $cs->internalExtractFiles(...explode(' ', $arg)));
    }
}
