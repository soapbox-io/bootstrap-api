<?php
    class TableRowElement extends BaseElement
    {
        private $content = <<<EOT
<tr class="alert {{type}}">
    {{fields}}
</tr>
EOT;

        private $type;
        private $fields;

        const INFO_TYPE = "alert-info";
        const SUCCESS_TYPE = "alert-success";
        const ERROR_TYPE = "alert-error";
        const WARNING_TYPE = "alert-block";

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

        public function getElementName()
        {
            return "tableRowElement";
        }

        public function getHTMLContent()
        {
            $htmlContent = $this->replaceTokens(array(
                                                     "type"  => $this->type,
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