<?php
    class TitleElement extends BaseElement
    {
        private $content = <<<EOT
<h1>{{title}}</h1>
<p class="lead">{{subTitle}}</p>
EOT;

        private $title;
        private $subTitle;

        public function setSubTitle($subTitle)
        {
            $this->subTitle = $subTitle;
        }

        public function getSubTitle()
        {
            return $this->subTitle;
        }

        public function setTitle($title)
        {
            $this->title = $title;
        }

        public function getTitle()
        {
            return $this->title;
        }

        public function getHTMLContent()
        {
            $htmlContent = $this->replaceTokens(array(
                                                     "title"    => $this->title,
                                                     "subTitle" => $this->subTitle,
                                                ), $this->content);

            return $htmlContent;
        }

        public function getElementName()
        {
            return "titleElement";
        }
    }

?>