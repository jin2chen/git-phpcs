<?php

namespace jin2chen\php\cs;

class PreCommitSniffer extends BaseCodeSniffer
{
    /**
     * @inheritdoc
     */
    public function extractFiles()
    {
        $command = join(' ', [$this->findGitCommand(), 'diff --cached --name-only --diff-filter=ACMR']);
        return explode("\n", `$command`);
    }
}
