<?php

namespace Keruald\Reporting\Output;

class HTMLOutput extends Output {

    private function makeId ($name) : string {
        return urlencode(strtolower(str_replace(' ', '-', $name)));
    }

    private static function encode (string $text) : string {
        return htmlspecialchars($text);
    }

    public function render () : string {

        $send = [];
        $title = $this->report->title;
        $send[] =
            '<h1 id="' . $this->makeId($title) . '">' . self::encode($title)
            . '</h1>';

        foreach ($this->report->sections as $section) {
            $title = $section->title;
            $send[] =
                '<h2 id="' . $this->makeId($title) . '">' .
                self::encode($title) . '</h2>';
            foreach ($section->entries as $entry) {
                $title = $entry->title;
                $send[] = '<h3 id="' . $this->makeId($title) . '">' .
                          self::encode($title) . '</h3>';

                $text = explode("\n\n", $entry->text);

                foreach ($text as $value) {
                    $send[] = '<p>' . self::encode($value) . '</p>';
                }
            }
        }
        $send[] = '<hr>';

        $send[] = '<h2 id="report-properties">Report properties</h2>';
        $send[] = '<table>';
        $send[] = str_repeat(" ", 4) . '<tbody>';

        $properties = $this->report->properties;

        foreach ($properties as $key => $value) {
            $send[] = str_repeat(" ", 4) . '<tr>';
            $send[] = str_repeat(" ", 8) .

                      '<th>' . self::encode($key) . '</th>';
            $send[] = str_repeat(" ", 8) .
                      '<td>' . self::encode($value) . '</td>';

            $send[] = str_repeat(" ", 4) . '</tr>';
        }
        $send[] = str_repeat(" ", 4) . '</tbody>';
        $send[] = '</table>';
        $send[] = '';

        return implode("\n", $send);
    }
}
