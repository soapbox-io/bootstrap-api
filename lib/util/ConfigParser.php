<?php
    /**
     *  Parses the Bootstrap API config file
     */
    class ConfigParser
    {
        /**
         * @static
         * @param SimpleXMLElement $config
         * @return null|TitleElement
         */
        public static function parseTitle(SimpleXMLElement $config)
        {
            if(empty($config))
                return null;

            $titleElement = new TitleElement();
            if(!isset($config->document))
            {
                // create default title if no definition is found
                $titleElement->setTitle("Default Title");
                $titleElement->setSubTitle("Add a <strong>&lt;document&gt;</strong> element to config.xml with
                                            <em>title</em> and <em>description</em> attributes");

                return $titleElement;
            }

            $titleElement->setTitle((string) @$config->document["title"]);
            $titleElement->setSubTitle((string) @$config->document["description"]);

            return $titleElement;
        }

        public static function parseSections(SimpleXMLElement $config)
        {
            if(empty($config))
                return array();

            $sections = array();
            if(!isset($config->sections))
            {
                // create one empty section if no definition is found
                $section = new SectionElement();
                $section->setIndex(1);
                $section->setTitle("Section Title");
                $section->setSubTitle("Add a <strong>&lt;section&gt;</strong> element to config.xml with
                                        <em>title</em> and <em>description</em> attributes");

                $sections[] = $section;
                return $sections;
            }

            $children = $config->sections->children();
            if(empty($children) || count($children) <= 0)
                return array();

            $counter = 1;
            foreach($children as $section)
            {
                if(empty($section))
                    continue;

                $sectionElement = new SectionElement();
                $sectionElement->setIndex($counter);
                $sectionElement->setTitle((string) @$section["title"]);
                $sectionElement->setSubTitle((string) @$section["subTitle"]);

                $requests = self::parseRequests($section);
                $sectionElement->setRequests($requests);

                $sections[] = $sectionElement;

                $counter++;
            }

            return $sections;
        }

        public static function parseSectionsList(array $sections)
        {
            if(empty($sections))
                return array();

            $listElements = array();
            foreach($sections as $section)
            {
                if(empty($section) || !is_a($section, "SectionElement"))
                    continue;

                $listElement = new SectionListElement();
                $listElement->setIndex($section->getIndex());
                $listElement->setTitle($section->getTitle());

                $listElements[] = $listElement;
            }

            return $listElements;
        }

        private static function parseRequests(SimpleXMLElement $section)
        {
            if(empty($section))
                return array();

            $requestElements = array();
            $requestChildren = $section->request;

            if(count($requestChildren) <= 0 || empty($requestChildren))
                return array();

            foreach($requestChildren as $request)
            {
                //                echo("<pre>".print_r($request, true)."</pre>");
                $verbs = (string) @$request["verbs"];

                $requestElement = new RequestElement();
                $requestElement->setUrl((string) @$request["url"]);
                $requestElement->setDescription(trim((string) @$request->description));
                $requestElement->setVerbs(self::parseHTTPVerbs($requestElement, $verbs));

                // remove description from request children for cleaner parsing
                unset($request->description);

                $elements = array();
                $children = $request->children();
                if(!empty($children) && count($children) > 0)
                {
                    foreach($children as $element)
                    {
                        if(empty($element))
                            continue;

                        $childElement = null;
                        $elementName = $element->getName();
                        switch($elementName)
                        {
                            case "code":
                                $childElement = self::parseCodeElement($element);
                                break;
                            case "alert":
                                $childElement = self::parseAlertElement($element);
                                break;
                            case "table":
                                $childElement = self::parseTableElement($element);
                                break;
                        }

                        if(!empty($childElement))
                            $elements[] = $childElement;
                    }
                }

                $requestElement->setElements($elements);

                $requestElements[] = $requestElement;
            }

            return $requestElements;
        }

        private static function parseHTTPVerbs(RequestElement $requestElement, $verbsStr)
        {
            if(empty($verbsStr))
                return array();

            $verbs = array();
            $verbsStr = explode(",", $verbsStr);
            foreach($verbsStr as $verb)
            {
                $verbs[] = $requestElement->getVerbByName($verb);
            }

            return $verbs;
        }

        private static function parseCodeElement(SimpleXMLElement $element)
        {
            $codeElement = new CodeElement();
            $codeElement->setTitle((string) @$element["title"]);
            $codeElement->setShowLineNumbers(((string) @$element["showLineNumbers"]) == "true");
            $codeElement->setCode(trim((string) @$element));

            return $codeElement;
        }

        private static function parseAlertElement(SimpleXMLElement $element)
        {
            $codeElement = new AlertBoxElement();
            $codeElement->setTitle((string) @$element["title"]);
            $codeElement->setType($codeElement->getTypeByName((string) @$element["type"]));
            $codeElement->setText(trim((string) @$element));

            return $codeElement;
        }

        private static function parseTableElement(SimpleXMLElement $element)
        {
            $tableElement = new TableElement();
            $tableElement->setTitle((string) @$element["title"]);
            $tableElement->setColumns(explode(",", (string) @$element["columns"]));

            $rows = $element->row;
            if(empty($rows) || count($rows) <= 0)
                return $tableElement;

            $rowElements = array();
            foreach($rows as $row)
            {
                if(empty($row))
                    continue;

                $rowElement = new TableRowElement();
                $rowElement->setType($rowElement->getTypeByName((string) @$row["type"]));

                $cells = $row->cell;

                $fields = array();

                if(!empty($cells) && count($cells) > 0)
                {
                    foreach($cells as $cell)
                    {
                        if(empty($cell))
                            continue;

                        $fields[] = (string) @$cell;
                    }
                }

                $numColumns = count($tableElement->getColumns());

                if(count($fields) < $numColumns)
                {
                    // fill with empty data if fields are missing
                    for($x = count($fields); $x < $numColumns; $x++)
                        $fields[] = "&nbsp;";
                }

                $rowElement->setFields($fields);

                $rowElements[] = $rowElement;
            }

            $tableElement->setRows($rowElements);

            return $tableElement;
        }

        /**
         * @static
         * @param array $errors
         * @return array
         */
        public static function parseErrors(array $errors)
        {
            $elements = array();

            $titleElement = new TitleElement();
            $titleElement->setTitle("Error parsing configuration file");
            $titleElement->setSubTitle("Please review the messages below");

            $elements[] = $titleElement;

            foreach($errors as $error)
            {
                //            echo "<pre>".trim($error->message)." on line ".$error->line."</pre>\n";

                $errorElement = new AlertBoxElement();
                $errorElement->setText(ucfirst(trim($error->message)) . " on <strong>line " . $error->line . "</strong>");

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

            return $elements;
        }

        public static function replaceTokens($elements, $content, $alertsAsExceptions=false, $ignoredSections=null)
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
    }

?>