<?php

namespace Portunus\Db;

class SqliteConnectionFactory
{
    private $packageDir;
    private $filename;
    private $dataDir;

    public function __construct($packageDir = null, $filename = null, $dataDir = null)
    {
        $this->packageDir = $packageDir;
        $this->filename = $filename;
        $this->dataDir = $dataDir;
    }

    public function setPackageDir($packageDir)
    {
        $this->packageDir = $packageDir;
    }

    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    public function setDataDir($dataDir)
    {
        $this->dataDir = $dataDir;
    }

    public function getConnection()
    {
        if (!preg_match('/^\//', $this->dataDir)) {
            $this->dataDir = $this->packageDir . $this->dataDir;
        }

        return [
            'driver' => 'pdo_sqlite',
            'path' => sprintf('%s/%s', $this->dataDir, $this->filename),
        ];
    }
}