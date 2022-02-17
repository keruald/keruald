<?php

namespace Keruald\Reporting\Output;

class MarkdownOutput extends Output {

    public function render () : string {


        $send = [];
        $send[] = '# ' . $this->report->title;
        $send[] = '';
        foreach ($this->report->sections as $section) {
            $send[] = '## ' . $section->title;
            $send[] = '';
            foreach ($section->entries as $entry) {

                $send[] = '### ' . $entry->title;
                $send[] = '';
                $send[] = $entry->text;
                $send[] = '';
            }
        }

        $send[] = '---';
        $send[] = '';
        $properties = $this->report->properties;
        $propertyMaxLength = 0;
        $maxValue = 0;
        foreach ($properties as $key => $value) {
            $propertyMaxLength = max($propertyMaxLength, strlen($key));
            $maxValue = max($maxValue, strlen($value));
        }
        if ($propertyMaxLength < 8) {
            $propertyMaxLength = 8;
        }

        $send[] = '| Property' . str_repeat(' ', $propertyMaxLength - 8) . ' | '
                  . str_repeat(' ', $maxValue) . ' |';

        $send[] = '|' . str_repeat('-', $propertyMaxLength + 2) . '|'
                  . str_repeat('-', $maxValue + 2) . '|';

        foreach ($properties as $key => $value) {
            $send[] =
                '| ' . $key . str_repeat(' ', $propertyMaxLength - strlen($key))
                . ' | '
                . $value . str_repeat(' ', $maxValue - strlen($value)) . ' |';
        }

        $send[] = '';

        return implode("\n", $send);
    }
}
