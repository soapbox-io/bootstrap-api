<?php
    class SectionListElement extends BaseElement
    {
        private $content = <<<EOT
<li><a href="#section{{index}}">{{title}}</a></li>
EOT;

        private $index;
        private $title;

        public function setIndex($index)
        {
            $this->index = $index;
        }

        public function getIndex()
        {
            return $this->index;
        }

        public function setTitle($name)
        {
            $this->title = $name;
        }

        public function getTitle()
        {
            return $this->title;
        }

        public function getHTMLContent()
        {
            $htmlContent = $this->replaceTokens(array(
                                                     "index" => $this->index,
                                                     "title"  => $this->title,
                                                ), $this->content);

            return $htmlContent;
        }

        public function getElementName()
        {
            return "sectionListElements";
        }
    }

?>