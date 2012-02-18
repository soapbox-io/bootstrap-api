<?php

    spl_autoload_register("autoload");

    // load HTML template
    $content = @file_get_contents(dirname(__FILE__) . "/template.html");

    // if the template is blank or cannot be read
    if(empty($content))
        die("The <strong>template.html</strong> file could not found or is not readable in " . dirname(__FILE__));

    // load configuration file (with CDATA sections being converted to strings)
    libxml_use_internal_errors(true);
    $config = simplexml_load_file(dirname(__FILE__) . "/config.xml", "SimpleXMLElement", LIBXML_NOCDATA);

    $ignoredSections = array("errorElements");

    // parse XML errors
    $errors = libxml_get_errors();

    if(!empty($errors))
    {
        $elements = ConfigParser::parseErrors($errors);

        // change the ignored sections to the other elements so they don't show when viewing errors
        $ignoredSections = array("sectionElements", "sectionListElements");
        $content = ConfigParser::replaceTokens($elements, $content, true, $ignoredSections);

        // final output to file - html in this case
        die($content);
    }

    // if the config is blank or cannot be read
    if(empty($config))
        die("<strong>config.xml</strong> file could not found or is not readable in " . dirname(__FILE__));

    $elements = array();

    // parse title
    $titleElement = ConfigParser::parseTitle($config);
    $elements[] = $titleElement;

    // parse section & section list elements
    $sectionElements = ConfigParser::parseSections($config);
    $sectionListElements = ConfigParser::parseSectionsList($sectionElements);

    $sectionElements = array_merge($sectionElements, $sectionListElements);

    if(!empty($sectionElements))
    {
        foreach($sectionElements as $element)
        {
            if(empty($element))
                continue;

            $elements[] = $element;
        }
    }


    $content = ConfigParser::replaceTokens($elements, $content, false, $ignoredSections);

    $isPreview = ((string) @$config["preview"] == "true");

    // final output to file

    if(!$isPreview)
    {
        $zip = new Zip("api-docs.zip");
        $zip->setComment("Created by soapbox.io bootstrap-api");

        $base = "api-docs";

        $zip->addFile("$base/index.html", $content);

        $zip->addFolder(dirname(__FILE__)."/assets", "$base/assets");
        $zip->addFolder(dirname(__FILE__)."/docs", "$base/docs");
        $zip->addFolder(dirname(__FILE__)."/img", "$base/img");
        $zip->addFolder(dirname(__FILE__)."/js", "$base/js");

        header("Content-type: application/octet-stream");
        header("Content-disposition: attachment; filename=api-docs.zip");

        echo $zip->getZipContents();
    }
    else
        echo $content;

    function autoload($class)
    {
        // check same directory
        $file = realpath(dirname(__FILE__) . "/" . $class . ".php");

        // if none found, check other directories
        if(!$file)
        {
            $searchDirectories = array(
                dirname(__FILE__),
                dirname(__FILE__) . "/lib/",
                dirname(__FILE__) . "/lib/elements",
                dirname(__FILE__) . "/lib/util",
            );

            foreach($searchDirectories as $dir)
            {
                $file = realpath($dir . "/" . $class . ".php");

                if($file)
                    break;
            }
        }

        // if found, require_once the sucker!
        if($file)
            require_once $file;
    }

?>