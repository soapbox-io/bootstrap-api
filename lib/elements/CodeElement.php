<?php
    class CodeElement extends BaseElement
    {
        private $content = <<<EOT
<blockquote>
    <h4>{{title}}</h4>
</blockquote>

<pre class="prettyprint well {{lineNumbers}}">{{code}}
</pre>
EOT;

        private $title;
        private $code;
        private $showLineNumbers;

        public function setCode($code)
        {
            $this->code = $code;
        }

        public function getCode()
        {
            return $this->code;
        }

        public function setTitle($title)
        {
            $this->title = $title;
        }

        public function getTitle()
        {
            return $this->title;
        }

        public function setShowLineNumbers($showLineNumbers)
        {
            $this->showLineNumbers = $showLineNumbers;
        }

        public function getShowLineNumbers()
        {
            return $this->showLineNumbers;
        }

        public function getElementName()
        {
            return "codeElement";
        }

        public function getHTMLContent()
        {
            $htmlContent = $this->replaceTokens(array(
                                                     "title"       => $this->title,
                                                     "code"        => trim(htmlentities($this->code)),
                                                     "lineNumbers" => $this->showLineNumbers ? "linenums" : "",
                                                ), $this->content);

            return $htmlContent;
        }
    }

?>