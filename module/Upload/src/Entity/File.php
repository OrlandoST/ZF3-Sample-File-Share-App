<?php
    namespace Upload\Entity;

    use Doctrine\ORM\Mapping as ORM;

    /**
     * @ORM\Entity
     * @ORM\Table(name="files")
     */
    class File
    {
        /**
         * @ORM\Id
         * @ORM\GeneratedValue
         * @ORM\Column(name="id")
         */
        protected $id;

        /**
         * @ORM\Column(name="file")
         */
        protected $file;

        /**
         * @ORM\Column(name="name")
         */
        protected $name;

        /**
         * @ORM\Column(name="password")
         */
        protected $password;

        /**
         * @ORM\Column(name="size")
         */
        protected $size;


        /**
         * @ORM\Column(name="type")
         */
        protected $type;

        /**
         * @ORM\Column(name="uploaded")
         */
        protected $uploaded;

        /**
         * @ORM\Column(name="downloaded", options={"default": 0})
         */
        protected $downloaded;

        public function __get($column)
        {
            if (property_exists($this, $column))
                return $this->$column;
        }

        public function __set($column, $value)
        {
            if (property_exists($this, $column))
                $this->$column = $value;

            return $this;
        }

    }