<?php
    abstract class BaseElement
    {
        /**
         * @param $tokens
         * @param $content
         *
         * @return mixed
         */
        public function replaceTokens(array $tokens, $content)
        {
            if(empty($tokens) || empty($content))
                return;

            foreach($tokens as $key => $value)
            {
                $content = str_replace("{{".$key."}}", $value, $content);
            }

            return $content;
        }

        /**
         * @override
         *
         * @return string
         */
        public function getHTMLContent()
        {
            return "";
        }

        /**
         * @override
         *
         * @return string
         */
        public function getElementName()
        {
            return "";
        }
    }
?>