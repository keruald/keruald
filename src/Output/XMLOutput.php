<?php


namespace Keruald\Reporting\Output;


class XMLOutput extends Output {

    public function render () : string {

        $document = xmlwriter_open_memory();

        xmlwriter_set_indent($document, true);
        xmlwriter_set_indent_string($document, '    ');

        xmlwriter_start_document($document, '1.0', 'UTF-8');

        xmlwriter_start_element($document, 'report');
        xmlwriter_start_attribute($document, 'title');
        xmlwriter_text($document, $this->report->title);
        xmlwriter_end_attribute($document);

        foreach ($this->report->sections as $section) {
            xmlwriter_start_element($document, 'section');
            xmlwriter_start_attribute($document, 'title');
            xmlwriter_text($document, $section->title);
            xmlwriter_end_attribute($document);

            foreach ($section->entries as $entry) {
                xmlwriter_start_element($document, 'entry');

                xmlwriter_start_attribute($document, 'title');
                xmlwriter_text($document, $entry->title);
                xmlwriter_end_attribute($document);

                xmlwriter_start_element($document, 'text');
                xmlwriter_text($document, $entry->text);
                xmlwriter_end_element($document);

                xmlwriter_end_element($document);
            }
            xmlwriter_end_element($document); // section
        }

        xmlwriter_start_element($document, 'data');
        xmlwriter_start_attribute($document, 'title');
        xmlwriter_text($document, "Properties");
        xmlwriter_end_attribute($document);

        foreach ($this->report->properties as $key => $value) {
            xmlwriter_start_element($document, 'entry');

            xmlwriter_start_element($document, 'key');
            xmlwriter_text($document, $key);
            xmlwriter_end_element($document);

            xmlwriter_start_element($document, 'value');
            xmlwriter_text($document, $value);
            xmlwriter_end_element($document);

            xmlwriter_end_element($document);
        }
        xmlwriter_end_element($document); // data

        xmlwriter_end_element($document); // report
        xmlwriter_end_document($document);

        return xmlwriter_output_memory($document);
    }

}
