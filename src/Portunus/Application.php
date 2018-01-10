<?php

namespace Portunus;

class Application
{
    public static function getAppDir()
    {
        return __DIR__.'/../..';
    }

    public static function getCacheDir()
    {
        return self::getAppDir().'/cache';
    }

    public static function getSrcDir()
    {
        return self::getAppDir().'/src';
    }

    public static function createDb(ContainerBuilder $container = null)
    {
        if (!$container) {
            $container = new ContainerBuilder();
        }

        $dataDir = Application::resolveRelativePath($container->getParameter('doctrine.db.data_dir'));

        if (!is_dir($dataDir)) {
            $result = mkdir($dataDir, 0777, true);
            if (!$result) {
                throw new \Exception('Error creating Portunus data dir');
            }
        }

        $dev = $container->getParameter('portunus.dev');
        $container->setParameter('protunus.dev', true); // schema creation requires dev mode
        $metadata = $container->get('doctrine.entity_manager')->getMetadataFactory()->getAllMetadata();

        // create db if not exists
        $dbName = $container->getParameter('doctrine.db.filename');
        $portunusDB = sprintf('%s/%s', $dataDir, $dbName);
        if (!file_exists($portunusDB)) {
            $container->get('doctrine.schema_tool')->createSchema($metadata);
        }

        // create proxy classes if not exist
        $cacheDir = $container->get('portunus.application')->getCacheDir();
        $cacheFiles = glob($cacheDir . '/__CG__*.php');
        if (count($cacheFiles) < 1) {
            $cacheDir = $container->get('portunus.application')->getCacheDir();
            $container->get('doctrine.entity_manager')->getProxyFactory()->generateProxyClasses($metadata, $cacheDir);
        }

        $container->setParameter('protunus.dev', $dev);
    }

    public static function resolveRelativePath($dataDir)
    {
        if (substr($dataDir, 0, 1) == '/') {
            return $dataDir;
        }

        $vendorDir = null;
        $baseDir = __DIR__ . '/../..';
        foreach (array($baseDir . '/../../autoload.php', $baseDir . '/../vendor/autoload.php', $baseDir . '/vendor/autoload.php') as $file) {
            if (file_exists($file)) {
                $vendorDir = realpath(dirname($file));
                break;
            }
        }

        return realpath(sprintf('%s/%s', $vendorDir,  $dataDir));
    }
}
