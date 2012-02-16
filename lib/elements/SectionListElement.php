<?php
    class SectionListElement extends BaseElement
    {
        private $content = <<<EOT
<li><a href="#subsection{{index}}">{{name}}</a></li>
EOT;

        private $index;
        private $name;

        public function setIndex($index)
        {
            $this->index = $index;
        }

        public function getIndex()
        {
            return $this->index;
        }

        public function setName($name)
        {
            $this->name = $name;
        }

        public function getName()
        {
            return $this->name;
        }

        public function getHTMLContent()
        {
            $htmlContent = $this->replaceTokens(array(
                                                 "index" => $this->index,
                                                 "name" => $this->name,
                                            ), $this->content);

            return $htmlContent;
        }

        public function getElementName()
        {
            return "sectionListElements";
        }
    }
?>