<?php
class FASTArchive
{
    //  Open FAST-archive and get XML content from 'outfrom.xml'
    //  Return: XML content or NULL
    public function Open($file_path)
    {
        $file_name = basename($file_path);

        if (strpos($file_name, '.fast') === FALSE)
            return NULL;

        $zip = new ZipArchive;
        
        if ($zip->open($file_path) === TRUE)
        {
            $xml_content = $zip->getFromName('outfrom.xml');
            $zip->close();

            if ($xml_content !== FALSE)
                return $xml_content;
        }

        return NULL;
    }
}
