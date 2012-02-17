<?php
    class AlertBoxElement extends BaseElement
    {
        private $content = <<<EOT
<div class="alert {{type}}">
    <h5>{{title}}</h5>

    <p>{{text}}</p>
</div>
EOT;

        private $title;
        private $type;
        private $text;

        const INFO_TYPE = "alert-info";
        const SUCCESS_TYPE = "alert-success";
        const ERROR_TYPE = "alert-error";
        const WARNING_TYPE = "alert-block";

        private $acceptableTypes = array(
            "warning" => self::WARNING_TYPE,
            "success" => self::SUCCESS_TYPE,
            "error" => self::ERROR_TYPE,
            "info" => self::INFO_TYPE,
        );

        public function setText($text)
        {
            $this->text = $text;
        }

        public function getText()
        {
            return $this->text;
        }

        public function setTitle($title)
        {
            $this->title = $title;
        }

        public function getTitle()
        {
            return $this->title;
        }

        public function setType($type)
        {
            $this->type = $type;
        }

        public function getType()
        {
            return $this->type;
        }

        public function getTypeByName($name)
        {
            if(empty($name))
                return null;

            if(!array_key_exists($name, $this->acceptableTypes))
                return null;

            return $this->acceptableTypes[$name];
        }

        public function getElementName()
        {
            return "alertBoxElement";
        }

        public function getHTMLContent()
        {
            $htmlContent = $this->replaceTokens(array(
                                                     "title" => $this->title,
                                                     "type"  => $this->type,
                                                     "text"  => trim($this->text),
                                                ), $this->content);

            return $htmlContent;
        }
    }

?>