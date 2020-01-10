<?php

namespace jin2chen\php\cs;

abstract class BaseCodeSniffer
{
    /**
     * The SHA1 ID of an empty branch.
     */
    const SHA1_EMPTY = '0000000000000000000000000000000000000000';

    /**
     * @var array
     */
    protected $files = [];
    /**
     * @var string
     */
    protected $realGitCommand;
    /**
     * @var string
     */
    protected $realPhpcsCommand;
    /**
     * @var string
     */
    protected $gitCommand = 'git';
    /**
     * @var string
     */
    protected $phpcsCommand = 'phpcs';

    /**
     * BaseCodeSniffer constructor.
     *
     * @param string $gitCommand
     * @param string $phpcsCommand
     */
    public function __construct($gitCommand = 'git', $phpcsCommand = 'phpcs')
    {
        $this->gitCommand = $gitCommand;
        $this->phpcsCommand = $phpcsCommand;
    }

    /**
     * Execute pre hook.
     *
     * @return integer
     */
    public function run()
    {
        if ($this->findPhpcsCommand() === false || $this->findGitCommand() === false) {
            return 0;
        }

        $this->files = $this->extractFiles();
        $this->filterFiles();
        $this->files = array_map('escapeshellarg', $this->files);
        $command = $this->findPhpcsCommand() . ' ' . join(' ', $this->files);
        passthru($command, $returnValue);

        return $returnValue;
    }

    /**
     * Find the phpcs command.
     *
     * @return string|boolean If found, return the path or return false.
     */
    public function findPhpcsCommand()
    {
        if ($this->realPhpcsCommand === null) {
            $phpcsCommand = getcwd() . '/vendor/bin/phpcs';
            if (is_executable($phpcsCommand)) {
                $this->realPhpcsCommand = $phpcsCommand;
            } elseif ($this->phpcsCommand[0] === '/' && is_executable($this->phpcsCommand)) {
                $this->realPhpcsCommand = $this->phpcsCommand;
            } elseif (static::commandExist($this->phpcsCommand)) {
                $this->realPhpcsCommand = $this->phpcsCommand;
            } else {
                $this->realPhpcsCommand = false;
            }
        }

        return $this->realPhpcsCommand;
    }

    /**
     * @return string|boolean
     */
    public function findGitCommand()
    {
        if ($this->realGitCommand === null) {
            if ($this->gitCommand[0] === '/' && is_executable($this->gitCommand)) {
                $this->realGitCommand = $this->gitCommand;
            } elseif (static::commandExist($this->gitCommand)) {
                $this->realGitCommand = $this->gitCommand;
            } else {
                $this->realGitCommand = false;
            }
        }

        return $this->realGitCommand;
    }

    /**
     * Extract files use command.
     *
     * @return array
     */
    abstract public function extractFiles();

    /**
     * Filter files.
     */
    private function filterFiles()
    {
        $this->files = array_unique($this->files);
        $this->filterEmptyOrNotExistFiles();
    }

    /**
     * Filter empty file, or none exists files.
     */
    private function filterEmptyOrNotExistFiles()
    {
        $this->files = array_filter($this->files, function ($file) {
            return !empty($file) && file_exists($file);
        });
    }

    /**
     * Check command exists.
     *
     * @param string $cmd
     * @return bool
     */
    public static function commandExist($cmd)
    {
        $cmd = escapeshellarg($cmd);
        $prefix = strpos(PHP_OS, 'WIN') === 0 ? 'where' : 'which';
        exec("{$prefix} {$cmd}", $output, $returnVal);
        return $returnVal === 0;
    }
}
