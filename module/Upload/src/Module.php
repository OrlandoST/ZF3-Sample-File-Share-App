<?php

    namespace Upload;

    use Zend\Db\Adapter\Adapter;
    use Zend\Db\Adapter\AdapterInterface;
    use Zend\Db\ResultSet\ResultSet;
    use Zend\Db\TableGateway\TableGateway;
    use Zend\ModuleManager\Feature\ConfigProviderInterface;

    use Upload\Model;

    class Module implements ConfigProviderInterface
    {
        public function getConfig()
        {
            return include __DIR__ . '/../config/module.config.php';
        }
    }