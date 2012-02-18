<?php

    spl_autoload_register("autoload");

    // load HTML template
    $content = @file_get_contents(dirname(__FILE__) . "/template.html");

    if(empty($content))
        die("The <strong>template.html</strong> file could not found or is not readable in ".dirname(__FILE__));

    // load configuration file (with CDATA sections being converted to strings)
    libxml_use_internal_errors(true);
    $config = simplexml_load_file(dirname(__FILE__) . "/config.xml", "SimpleXMLElement", LIBXML_NOCDATA);

    $ignoredSections = array("errorElements");

    $errors = libxml_get_errors();

    if(!empty($errors))
    {
        $elements = array();

        $ignoredSections = array("sectionElements", "sectionListElements");

        $titleElement = new TitleElement();
        $titleElement->setTitle("Error parsing configuration file");
        $titleElement->setSubTitle("Please review the messages below");

        $elements[] = $titleElement;

        foreach($errors as $error)
        {
//            echo "<pre>".trim($error->message)." on line ".$error->line."</pre>\n";

            $errorElement = new AlertBoxElement();
            $errorElement->setText(ucfirst(trim($error->message))." on <strong>line ".$error->line."</strong>");

            $severity = $error->level;
            switch($severity)
            {
                default:
                case LIBXML_ERR_WARNING:
                    $errorElement->setTitle("Notice");
                    $errorElement->setType(AlertBoxElement::INFO_TYPE);
                    break;
                case LIBXML_ERR_ERROR:
                    $errorElement->setTitle("Warning");
                    $errorElement->setType(AlertBoxElement::WARNING_TYPE);
                    break;
                case LIBXML_ERR_FATAL:
                    $errorElement->setTitle("Fatal Error");
                    $errorElement->setType(AlertBoxElement::ERROR_TYPE);
                    break;
            }

            $elements[] = $errorElement;
        }

        $content = replaceTokens($elements, $content, true, $ignoredSections);

        // final output to file - html in this case
        die($content);
    }
    else
    {

    }

    if(empty($config))
        die("<strong>config.xml</strong> file could not found or is not readable in ".dirname(__FILE__));

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

    $content = replaceTokens($elements, $content, false, $ignoredSections);

    // final output to file - html in this case
    echo $content;

    function replaceTokens($elements, $content, $alertsAsExceptions=false, $ignoredSections=null)
    {
        // find common element names, and group them
        $groups = array();

        foreach($elements as $element)
        {
            if(empty($element))
                continue;

            $elementName = $element->getElementName();
            if($elementName == "alertBoxElement" && $alertsAsExceptions)
                $elementName = "errorElements";

            if(!isset($groups[$elementName]))
                $groups[$elementName] = array();

            $groups[$elementName][] = $element;
        }

        // ignore sections by replacing their placeholders with blank data
        if(!empty($ignoredSections))
        {
            foreach($ignoredSections as $ignored)
                $content = str_replace("{{" . $ignored . "}}", "", $content);
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