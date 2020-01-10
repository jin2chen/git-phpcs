<?php

namespace mole\php\cs;

class PrePushSniffer extends BaseCodeSniffer
{
    /**
     * @inheritdoc
     */
    public function extractFiles()
    {
        $files = [];
        while ($arg = trim(fgets(STDIN))) {
            $files = array_merge($files, $this->internalExtractFiles(...explode(' ', $arg)));
        }

        return $files;
    }

    /**
     * @param string $localRef
     * @param string $localSha
     * @param string $remoteRef
     * @param string $remoteSha
     * @return array
     */
    public function internalExtractFiles($localRef, $localSha, /** @noinspection PhpUnusedParameterInspection */ $remoteRef, $remoteSha)
    {
        $files = [];
        if ($localRef === '(delete)' || $localSha === self::SHA1_EMPTY) {
            return $files;
        }

        $localSha = escapeshellarg($localSha);
        $remoteSha = escapeshellarg($remoteSha);
        $command = join(' ', [$this->findGitCommand(), 'diff-tree --no-commit-id --name-only -r', $localSha, $remoteSha]);
        $files = explode("\n", `$command`);

        return $files;
    }
}
