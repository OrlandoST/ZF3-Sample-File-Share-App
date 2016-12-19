<?php
    /*
     * this service isn't actually used – replaced with Doctrine ORM
     */

    namespace Upload\Service;

    use Zend\Db\ResultSet\ResultSet;
    use Zend\Db\Adapter\Adapter;

    use Upload\Model\File;

    class FileManager
    {
        protected $fileDir = './data/upload/';
        protected $adapter;

        public function __construct()
        {
            $this->adapter = new Adapter([
                'driver' => 'Pdo_Mysql',
                'database' => 'swift',
                'username' => 'swift',
                'password' => ''
            ]);
        }

        protected function updateFileDownloadsCount($id)
        {
            $this->adapter->createStatement("UPDATE `files` SET `downloaded` = `downloaded` + 1 WHERE `id` = ? LIMIT 1;", [$id])->execute();
        }

        public function getFiles()
        {
            $result = $this->adapter->createStatement("SELECT * FROM `files`;")->execute();

            $resultSet = new ResultSet();
            $resultSet->initialize($result);

            $resultSet->setArrayObjectPrototype(new File());

            return $resultSet->toArray();
        }

        public function getFile($id)
        {
            $file = false;

            $result = $this->adapter->createStatement("SELECT * FROM `files` WHERE `id` = ? LIMIT 1;", [$id])->execute();

            $resultSet = new ResultSet();
            $resultSet->initialize($result);

            $resultSet->setArrayObjectPrototype(new File());

            if ($resultSet->count())
            {
                $file = $resultSet->current();

                $this->updateFileDownloadsCount($id);
            }

            return $file;
        }

        public function getFileContents(File $file)
        {
            return file_get_contents($this->fileDir . $file->file);
        }

        public function saveFile(File $file)
        {
            $data = array(
                $file->file,
                $file->name,
                $file->size,
                $file->type,
                $file->password
            );

            $this->adapter->
                createStatement("INSERT INTO `files` (`file`, `name`, `size`, `type`, `password`) VALUES(?, ?, ?, ?, ?);", $data)->
                execute();

            return $this->adapter->getDriver()->getLastGeneratedValue();
        }

        public function deleteFile($id)
        {
            if ($file = $this->getFile($id))
            {
                $this->adapter->createStatement("DELETE FROM `files` WHERE `id` = ? LIMIT 1;", [$id])->execute();
                unlink($this->fileDir . $file->file);

                return true;
            }

            return false;
        }
    }