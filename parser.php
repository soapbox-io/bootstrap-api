<?php

    spl_autoload_register("autoload");

    // load HTML template
    $content = file_get_contents(dirname(__FILE__) . "/template.html");

    // load configuration file (with CDATA sections being converted to strings)
    $config = simplexml_load_file(dirname(__FILE__) . "/config.xml", "SimpleXMLElement", LIBXML_NOCDATA);

    if(empty($config))
        die("config.xml file could not found or is not readable in ".dirname(__FILE__));

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

    $content = replaceTokens($elements, $content);

    // final output to file - html in this case
    echo $content;

    function replaceTokens($elements, $content)
    {
        // find common element names, and group them
        $groups = array();

        foreach($elements as $element)
        {
            if(empty($element))
                continue;

            if(!isset($groups[$element->getElementName()]))
                $groups[$element->getElementName()] = array();

            $groups[$element->getElementName()][] = $element;
        }

        if(empty($groups))
            return $content;

        foreach($groups as $type => $elements)
        {
            $combinedElements = "";
            if(empty($type) || empty($elements))
                continue;

            foreach($elements as $element)
            {
                if(empty($element))
                    continue;

                $combinedElements .= $element->getHTMLContent() . "\n";
            }

            $content = str_replace("{{" . $type . "}}", $combinedElements, $content);
        }

        return $content;
    }

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