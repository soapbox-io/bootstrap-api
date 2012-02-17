<?php
    class TableElement extends BaseElement
    {
        private $content = <<<EOT
<blockquote>
    <h4>{{title}}</h4>
</blockquote>

<table class="table table-bordered">
    <thead>
    <tr>
        {{columns}}
    </tr>
    </thead>
    <tbody>
        {{rows}}
    </tbody>
</table>
EOT;

        private $title;
        private $columns;
        private $rows;

        public function setTitle($title)
        {
            $this->title = $title;
        }

        public function getTitle()
        {
            return $this->title;
        }

        public function setColumns(array $columns)
        {
            $this->columns = $columns;
        }

        public function getColumns()
        {
            return $this->columns;
        }

        public function setRows($rows)
        {
            $this->rows = $rows;
        }

        public function getRows()
        {
            return $this->rows;
        }

        public function getElementName()
        {
            return "tableElement";
        }

        public function getHTMLContent()
        {
            $htmlContent = $this->replaceTokens(array(
                                                     "title"    => $this->title,
                                                     "columns"  => $this->parseColumns(),
                                                     "rows"     => $this->parseRows(),
                                                ), $this->content);

            return $htmlContent;
        }

        private function parseColumns()
        {
            if(empty($this->columns))
                return "";

            $columns = "";
            foreach($this->columns as $column)
            {
                $columns .= "<th>$column</th>\n";
            }

            return $columns;
        }

        private function parseRows()
        {
            if(empty($this->rows))
                return "";

            $rows = "";
            foreach($this->rows as $row)
            {
                if(empty($row) || !$row || !is_a($row, "TableRowElement"))
                    continue;

                $rows .= $row->getHTMLContent() . "\n";
            }

            return $rows;
        }
    }

?>