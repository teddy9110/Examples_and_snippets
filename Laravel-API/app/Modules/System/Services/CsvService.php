<?php

namespace Rhf\Modules\System\Services;

class CsvService
{
    protected $filepath;

    /**
     * Create a new CsvService instance.
     *
     * @param string
     * @return void
     */
    public function __construct($filename)
    {
        $this->filepath = storage_path($filename);
    }


    /**************************************************
    *
    * PUBLIC METHODS
    *
    ***************************************************/

    /**
     * Retrieve CSV as array with headers as key.
     *
     * @return array
     */
    public function toArray()
    {
        $csv = array();

        if (($file = fopen($this->filepath, 'r')) === false) {
            throw new \Exception('There was an error loading the CSV file.');
        } else {
            $headers = [];
            while (($line = fgetcsv($file, 1000)) !== false) {
                // Set the headers
                if (empty($headers)) {
                    $headers = $line;
                    continue;
                }

                // Assign a row
                $row = [];
                foreach ($headers as $key => $header) {
                    $row[$header] = $line[$key];
                }

                $csv[] = $row;
            }

            fclose($file);
        }

        return $csv;
    }
}
