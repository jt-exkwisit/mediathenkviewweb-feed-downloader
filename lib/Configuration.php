<?php

class Configuration
{
    private $_scriptPath = "";
    private $_libPath = "";
    private $_filesPath = "";
    private $_feedArgs = "";
    public $_umlautSearch = ['ä','ö','ü','ß'];
    public $_umlautReplace = ['ae','oe','ue','ss'];

    public function setScriptPath(string $path): void
    {
        $this->_scriptPath = $path;
        $this->_libPath = $this->_scriptPath . "/lib";
    }

    public function setFilesPath(string $path): void
    {
        if ($this->isAbsolutePath($path))
            $this->_filesPath = $path;
        else
            $this->_filesPath = $this->_scriptPath . substr($path, 1);
    }
    public function setFeedArgs(array $args): string
    {
        if( count($args) > 2 )
            die("To many command line arguments, please provide just one.");
        if( count($args) < 2 )
            die("To view command line arguments, please provide at least one.");
        return $this->_feedArgs = $args[1];
    }

    public function getScriptPath(): string
    {
        return $this->_scriptPath;
    }

    public function getLibPath(): string
    {
        return $this->_libPath;
    }

    public function getFilesPath(): string
    {
        return $this->_filesPath;
    }
    public function getFeedArgs(): string
    {
        return $this->_feedArgs;
    }


    private function isAbsolutePath(string $file): bool
    {
        return strspn($file, '/\\', 0, 1)
            || (strlen($file) > 3 && ctype_alpha($file[0])
                && substr($file, 1, 1) === ':'
                && strspn($file, '/\\', 2, 1)
            )
            || null !== parse_url($file, PHP_URL_SCHEME);
    }

}
