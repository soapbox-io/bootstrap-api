<?php
    class SectionElement extends BaseElement
    {
        private $content = <<<EOT
<section id="section{{index}}">

    <div class="page-header">

        <h1>{{title}}
            <small>{{subTitle}}</small>
        </h1>
    </div>

    {{requests}}

</section>
EOT;

        private $index;
        private $title;
        private $subTitle;

        /**
         * @var array An array of RequestElement instances
         */
        private $requests;


        public function setIndex($index)
        {
            $this->index = $index;
        }

        public function getIndex()
        {
            return $this->index;
        }

        /**
         * @param array $requests
         */
        public function setRequests($requests)
        {
            $this->requests = $requests;
        }

        /**
         * @return array
         */
        public function getRequests()
        {
            return $this->requests;
        }

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

        public function getElementName()
        {
            return "sectionElements";
        }

        public function getHTMLContent()
        {
            $htmlContent = $this->replaceTokens(array(
                                                     "index"    => $this->index,
                                                     "title"    => $this->title,
                                                     "subTitle" => $this->subTitle,
                                                     "requests" => $this->parseRequests()
                                                ), $this->content);

            return $htmlContent;
        }

        private function parseRequests()
        {
            if(empty($this->requests))
                return "";

            $requests = "";

            foreach($this->requests as $request)
            {
                if(empty($request) || !$request || !is_a($request, "RequestElement"))
                    continue;

                $requests .= $request->getHTMLContent() . "\n";
            }

            return $requests;
        }
    }

?>