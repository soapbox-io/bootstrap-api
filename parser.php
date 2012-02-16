<?php

    spl_autoload_register("autoload");

    function autoload($class)
    {
        // check same directory
        $file = realpath(dirname(__FILE__) . "/" . $class . ".php");

        // if none found, check other directories
        if (!$file)
        {
            $searchDirectories = array(
                dirname(__FILE__),
                dirname(__FILE__)."/lib/",
                dirname(__FILE__)."/lib/elements",
            );

            foreach ($searchDirectories as $dir)
            {
                $file = realpath($dir . "/" . $class . ".php");

                if($file)
                    break;
            }
        }

        // if found, require_once the sucker!
        if ($file)
            require_once $file;
    }

?>