<?php
    /**
     * Inspired by PHPZip
     * @see https://github.com/Grandt/PHPZip
     */
    class Zip
    {
        /**
         * @var ZipArchive
         */
        private $zipArchive;

        private $tempFileName;
        private $ready = false;

        public $filename;

        public function __construct($filename)
        {
            $this->zipArchive = new ZipArchive();
            $this->filename = $filename;

            $this->tempFileName = @tempnam("", $this->filename);
            if(!realpath($this->tempFileName))
                throw new Exception("Cannot create temporary file");

            $this->initialize();
        }

        private function initialize()
        {
            try
            {
                $resource = $this->zipArchive->open($this->tempFileName, ZipArchive::CREATE);

                if($resource === true)
                    $this->ready = true;
            }
            catch(Exception $e)
            {
                throw new Exception("Cannot create ZipArchive");
            }
        }

        public function setComment($comment)
        {
            $this->checkStatus();

            $this->zipArchive->setArchiveComment($comment);
        }

        public function addFile($name, $content)
        {
            $this->checkStatus();

            $this->zipArchive->addFromString($name, $content);
        }

        public function addFolder($path, $name)
        {
            $this->checkStatus();

            $this->addDirectoryToZip($path, $name);
        }

        public function getZipContents()
        {
            $this->checkStatus();

            $this->zipArchive->close();
            return file_get_contents($this->tempFileName);
        }

        private function checkStatus()
        {
            if(!$this->ready)
                throw new Exception("ZipArchive is not ready");
        }

        private function addDirectoryToZip($directory, $name)
        {
            if(file_exists($directory))
            {
                if(is_dir($directory))
                {
                    $this->zipArchive->addEmptyDir($name);
                }

                $iter = new DirectoryIterator($directory);
                foreach($iter as $file)
                {
                    if($file->isDot())
                        continue;

                    $newRealPath = $file->getPathname();
                    $newZipPath = $this->pathJoin($name, $file->getFilename());

                    if(file_exists($newRealPath))
                    {
                        if($file->isFile())
                        {
                            $this->zipArchive->addFile($file->getPathname(), "$newZipPath");
                        }
                        else
                        {
                            $this->addDirectoryToZip($newRealPath, $newZipPath);
                        }
                    }
                }
            }
        }

        /**
         * Join $file to $dir path, and clean up any excess slashes.
         *
         * @param String $dir
         * @param String $file
         * @return String
         */
        private function pathJoin($dir, $file)
        {
            if(empty($dir) || empty($file))
            {
                return $this->getRelativePath($dir . $file);
            }
            return $this->getRelativePath($dir . '/' . $file);
        }

        /**
         * Clean up a path, removing any unnecessary elements such as /./, // or redundant ../ segments.
         * If the path starts with a "/", it is deemed an absolute path and any /../ in the beginning is stripped off.
         * The returned path will not end in a "/".
         *
         * @param $path
         * @return String the clean path
         */
        private function getRelativePath($path)
        {
            $path = preg_replace("#/+\.?/+#", "/", str_replace("\\", "/", $path));
            $dirs = explode("/", rtrim(preg_replace('#^(?:\./)+#', '', $path), '/'));

            $offset = 0;
            $sub = 0;
            $subOffset = 0;
            $root = "";

            if(empty($dirs[0]))
            {
                $root = "/";
                $dirs = array_splice($dirs, 1);
            } else if(preg_match("#[A-Za-z]:#", $dirs[0]))
            {
                $root = strtoupper($dirs[0]) . "/";
                $dirs = array_splice($dirs, 1);
            }

            $newDirs = array();
            foreach($dirs as $dir)
            {
                if($dir !== "..")
                {
                    $subOffset--;
                    $newDirs[++$offset] = $dir;
                } else
                {
                    $subOffset++;
                    if(--$offset < 0)
                    {
                        $offset = 0;
                        if($subOffset > $sub)
                        {
                            $sub++;
                        }
                    }
                }
            }

            if(empty($root))
            {
                $root = str_repeat("../", $sub);
            }
            return $root . implode("/", array_slice($newDirs, 0, $offset));
        }
    }

?>