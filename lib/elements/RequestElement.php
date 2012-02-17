<?php
    class RequestElement extends BaseElement
    {
        private $content = <<<EOT
<div class="row">
    <div class="span12">
        <div class="breadcrumb">
            <h3>{{url}}
                <small>{{description}}</small>
            </h3>

            {{verbs}}
        </div>

        {{elements}}
    </div>
</div>
EOT;

        private $url;
        private $description;

        /**
         * @var array
         */
        private $verbs;

        /**
         * @var array An array of any BaseElement-extending instances
         */
        private $elements;

        const GET_VERB = '<span class="label label-success">GET</span>';
        const PUT_VERB = '<span class="label label-warning">PUT</span>';
        const POST_VERB = '<span class="label label-info">POST</span>';
        const DELETE_VERB = '<span class="label label-important">DELETE</span>';

        private $acceptableVerbs = array("GET"    => self::GET_VERB,
                                         "POST"   => self::POST_VERB,
                                         "PUT"    => self::PUT_VERB,
                                         "DELETE" => self::DELETE_VERB);

        public function setDescription($description)
        {
            $this->description = $description;
        }

        public function getDescription()
        {
            return $this->description;
        }

        public function setUrl($url)
        {
            $this->url = $url;
        }

        public function getUrl()
        {
            return $this->url;
        }

        public function setVerbs($verbs)
        {
            $this->verbs = $verbs;
        }

        public function getVerbs()
        {
            return $this->verbs;
        }

        /**
         * @param array $elements
         */
        public function setElements($elements)
        {
            $this->elements = $elements;
        }

        /**
         * @return array
         */
        public function getElements()
        {
            return $this->elements;
        }

        public function getElementName()
        {
            return "requestElements";
        }

        public function getVerbByName($verb)
        {
            if(empty($verb))
                return null;

            $verb = strtoupper($verb);
            if(!array_key_exists($verb, $this->acceptableVerbs))
                return "<span class=\"label\">$verb</span>";

            return $this->acceptableVerbs[$verb];
        }

        public function getHTMLContent()
        {
            $htmlContent = $this->replaceTokens(array(
                                                     "url"            => trim($this->url),
                                                     "description"    => trim($this->description),
                                                     "verbs"          => $this->parseVerbs(),
                                                     "elements"       => $this->parseElements(),
                                                ), $this->content);

            return $htmlContent . "\n<br/>\n";
        }

        private function parseVerbs()
        {
            if(empty($this->verbs))
                return "";

            return implode("\n", $this->verbs);
        }

        private function parseElements()
        {
            if(empty($this->elements))
                return null;

            $elementHTML = "";

            foreach($this->elements as $element)
            {
                if(!$element || empty($element) || !is_subclass_of($element, "BaseElement"))
                    continue;

                $elementHTML .= $element->getHTMLContent() . "\n";
            }

            return $elementHTML;
        }
    }

?>