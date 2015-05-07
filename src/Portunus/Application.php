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

        $dataDir = $container->getParameter('doctrine.db.data_dir');
        $dbName = $container->getParameter('doctrine.db.filename');

        if (!is_dir($dataDir)) {
            $result = mkdir($dataDir, 0777, true);
            if (!$result) {
                throw new \Exception('Error creating Portunus data dir');
            }
        }

        // return if db file exists
        $portunusDB = sprintf('%s/%s', $dataDir, $dbName);
        if (file_exists($portunusDB)) {
            return;
        }

        $dev = $container->getParameter('portunus.dev');
        $container->setParameter('protunus.dev', true); // schema creation requires dev mode
        $metadata = $container->get('doctrine.entity_manager')->getMetadataFactory()->getAllMetadata();
        $container->get('doctrine.schema_tool')->createSchema($metadata);
        $cacheDir = $container->get('portunus.application')->getCacheDir();
        $container->get('doctrine.entity_manager')->getProxyFactory()->generateProxyClasses($metadata, $cacheDir);
        $container->setParameter('protunus.dev', $dev);
    }
}