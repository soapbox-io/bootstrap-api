<?php
    class TableRowElement extends BaseElement
    {
        private $content = <<<EOT
<tr class="{{type}}">
    {{fields}}
</tr>
EOT;

        private $type;
        private $fields;

        const INFO_TYPE = "alert alert-info";
        const SUCCESS_TYPE = "alert alert-success";
        const ERROR_TYPE = "alert alert-error";
        const WARNING_TYPE = "alert alert-block";

        private $acceptableTypes = array(
            "warning" => self::WARNING_TYPE,
            "success" => self::SUCCESS_TYPE,
            "error" => self::ERROR_TYPE,
            "info" => self::INFO_TYPE,
        );

        public function setFields(array $fields)
        {
            $this->fields = $fields;
        }

        public function getFields()
        {
            return $this->fields;
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
            return "tableRowElement";
        }

        public function getHTMLContent()
        {
            $htmlContent = $this->replaceTokens(array(
                                                     "type"    => $this->type,
                                                     "fields"  => $this->parseFields()
                                                ), $this->content);

            return $htmlContent;
        }

        private function parseFields()
        {

            if(empty($this->fields))
                return "";

            $fields = "";
            foreach($this->fields as $field)
            {
                $fields .= "<td>$field</td>\n";
            }

            return $fields;
        }
    }

?>