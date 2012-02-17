<?php

// ------------------------------- Dependencies ----------------------------------

    spl_autoload_register("autoload");

// ------------------------------- File IO ---------------------------------------

    $content = file_get_contents(dirname(__FILE__) . "/index.html");

    $title = new TitleElement();
    $title->setTitle("soapbox.io");
    $title->setSubTitle("Providing sexy fucking APIs");

    $sectionListElement1 = new SectionListElement();
    $sectionListElement1->setIndex(1);
    $sectionListElement1->setName("User API");

    $sectionListElement2 = new SectionListElement();
    $sectionListElement2->setIndex(2);
    $sectionListElement2->setName("Image API");

    $section = new SectionElement();
    $section->setIndex(1);
    $section->setTitle("User API");
    $section->setSubTitle("An API for getting User data");

    // user get request
    $userGetReq = new RequestElement();
    $userGetReq->setUrl("/user/get");
    $userGetReq->setDescription("Retrieve a particular user");
    $userGetReq->setVerbs(array(
                               $userGetReq->getVerbByName("GET"),
                               $userGetReq->getVerbByName("POST"),
                               $userGetReq->getVerbByName("PUT"),
                               $userGetReq->getVerbByName("DELETE"),
                               $userGetReq->getVerbByName("PATCH"),
                               $userGetReq->getVerbByName("HEAD"),
                          ));

    $codeSnippet = new CodeElement();
    $codeSnippet->setShowLineNumbers(true);
    $codeSnippet->setTitle("How this works...");

    // how to build a complex object
    $codeSnippet->setCode('$userSaveReq = new RequestElement();
$userSaveReq->setUrl("/user/get");
$userSaveReq->setDescription("Save a new user");
$userSaveReq->setVerbs(array($userSaveReq->getVerbByName("POST")));

$section->setRequests(array($userGetReq, $userSaveReq));


$content = replaceTokens(array($title, $sectionListElement1, $section), $content);');

    $userGetReq->setElements(array($codeSnippet));

    $userSaveReq = new RequestElement();
    $userSaveReq->setUrl("/user/save");
    $userSaveReq->setDescription("Save a <em>new</em> user");
    $userSaveReq->setVerbs(array($userSaveReq->getVerbByName("POST")));

    $warningElement = new AlertBoxElement();
    $warningElement->setTitle("WARNING");
    $warningElement->setType(AlertBoxElement::WARNING_TYPE);
    $warningElement->setText("Be careful... You may get a boner.");

    $successElement = new AlertBoxElement();
    $successElement->setTitle("Aaaah :)");
    $successElement->setType(AlertBoxElement::SUCCESS_TYPE);
    $successElement->setText("Isn't life beautiful?");

    $tableElement = new TableElement();
    $tableElement->setTitle("Here's a table example");
    $tableElement->setColumns(array("Column 1", "Column 2"));

    $tableRowElement = new TableRowElement();
    $tableRowElement->setFields(array("A", "B"));
    $tableRowElement->setType(TableRowElement::INFO_TYPE);

    $tableRowElement2 = new TableRowElement();
    $tableRowElement2->setFields(array("C", "D"));
    $tableRowElement2->setType(TableRowElement::ERROR_TYPE);

    $tableRowElement3 = new TableRowElement();
    $tableRowElement3->setFields(array("E", "F"));

    $tableElement->setRows(array($tableRowElement, $tableRowElement2, $tableRowElement3));

    $userSaveReq->setElements(array($warningElement, $successElement, $tableElement));

    $section->setRequests(array($userGetReq, $userSaveReq));

    $section2 = new SectionElement();
    $section2->setIndex(2);
    $section2->setTitle("Image API");
    $section2->setSubTitle("An API for getting Image data");

    // image get request
    $imageGetReq = new RequestElement();
    $imageGetReq->setUrl("/image/get");
    $imageGetReq->setDescription("Retrieve a particular image");
    $imageGetReq->setVerbs(array(
                               $imageGetReq->getVerbByName("POST")
                          ));

    $codeSnippet2 = new CodeElement();
    $codeSnippet2->setShowLineNumbers(false);
    $codeSnippet2->setTitle("How this works...");
    $codeSnippet2->setCode('HTTP/1.1 200 OK
Date: Thu, 16 Feb 2012 11:26:02 GMT
Server: Apache/2.2.14 (Unix) DAV/2 mod_ssl/2.2.14 OpenSSL/0.9.8l PHP/5.3.1 mod_perl/2.0.4 Perl/v5.10.1
X-Powered-By: PHP/5.3.1
Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0
Pragma: no-cache
Content-Length: 226
Access-Control-Allow-Origin: *
Content-Encoding: gzip
Vary: Accept-Encoding
Content-Type: application/json
Expires: 0

{"id":"4135","firstName":"Danny","lastName":"Kopping","email":"danny@dannykopping.co.za","password":"","type":"Recruiter\/Employer","active":true,"verified":true,"accountVerificationCodeId":null,"createdAt":"2012-02-16 13:26:03","updatedAt":"2012-02-16 13:26:03","_explicitType":"za.co.rsajobs.vo.User"}');

    $imageGetReq->setElements(array($codeSnippet2));

    $section2->setRequests(array($imageGetReq));



// ------------------------------- Output ---------------------------------------

    // Add a all the pieces to the "page" and replace tokens in existing html content
    $content = replaceTokens(array($title, $sectionListElement1,
                                  $sectionListElement2, $section, $section2), $content);

// ------------------------------- File IO ---------------------------------------

    // final output to file - html in this case
    echo $content;








    function replaceTokens($elements, $content)
    {
        // find common element names, and group them
        $groups = array();

        foreach ($elements as $element)
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

                $combinedElements .= $element->getHTMLContent()."\n";
            }

            $content = str_replace("{{".$type."}}", $combinedElements, $content);
        }

        return $content;
    }

    function autoload($class)
    {
        // check same directory
        $file = realpath(dirname(__FILE__) . "/" . $class . ".php");

        // if none found, check other directories
        if (!$file)
        {
            $searchDirectories = array(
                dirname(__FILE__),
                dirname(__FILE__) . "/lib/",
                dirname(__FILE__) . "/lib/elements",
            );

            foreach ($searchDirectories as $dir)
            {
                $file = realpath($dir . "/" . $class . ".php");

                if ($file)
                    break;
            }
        }

        // if found, require_once the sucker!
        if ($file)
            require_once $file;
    }

?>