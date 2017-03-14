<?php
/*
The MIT License (MIT)

Copyright © 2017 ALEXANDER BIVZYUK

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and
associated documentation files (the “Software”), to deal in the Software without restriction, including
without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following
conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions
of the Software.

THE SOFTWARE IS PROVIDED “AS IS”, WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR
PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT
OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
OTHER DEALINGS IN THE SOFTWARE.
*/

//Arguments:
//  $fileArray - must contain $_FILES array
//  $inputName - must contain value of the attribute 'name' in the tag 'input'
//  $saveUploadedFile - save uploaded file or not
//  $savePath - path to save the uploaded files (folder must be exists)
function ProccessFile($fileArray, $inputName, $saveUploadedFile = false, $savePath = '')
{
  if ($fileArray == NULL || $fileArray[$inputName] == NULL) return NULL;

  $file     = $fileArray[$inputName];
  $fileName = basename($file['name']);
  $filePath = $file['tmp_name'];
  $fileType = $file['type'];

  if ($fileType == 'text/xml')
  {
    if ($saveUploadedFile)
      move_uploaded_file($filePath, $savePath.$fileName);

    return file_get_contents($filePath);
  }

  if ($fileType == 'application/octet-stream' || $fileType == 'application/zip' || strpos($fileName, '.fast') !== false)
  {
    $zipArchive = zip_open($filePath);
    if ($zipArchive)
    {
      while ($zipEntry = zip_read($zipArchive))
      {
        if (zip_entry_name($zipEntry) != 'outfrom.xml')
          continue;

        if (zip_entry_open($zipArchive, $zipEntry, "r"))
        {
          $xmlContent = zip_entry_read($zipEntry, zip_entry_filesize($zipEntry));
          zip_close($zipArchive);

          if ($saveUploadedFile)
            move_uploaded_file($filePath, $savePath.$fileName);

          return $xmlContent;
        }
      }
    }
    zip_close($zipArchive);
  }

  return NULL;
}
