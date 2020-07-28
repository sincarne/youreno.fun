<?php

  # https://www.the-art-of-web.com/php/directory-list/
  function getFileList($dir)
  {
    // array to hold return value
    $retval = [];

    // add trailing slash if missing
    if(substr($dir, -1) != "/") {
      $dir .= "/";
    }

    // open pointer to directory and read list of files
    $d = @dir($dir) or die("getFileList: Failed opening directory {$dir} for reading");
    while(FALSE !== ($entry = $d->read())) {
      // skip hidden files
      if($entry{0} == ".") continue;
      if(is_dir("{$dir}{$entry}")) {
        $retval[] = [
          'name' => "{$entry}/",
          'type' => filetype("{$dir}{$entry}"),
          'size' => 0,
          'lastmod' => filemtime("{$dir}{$entry}")
        ];
      } elseif(is_readable("{$dir}{$entry}")) {
        $retval[] = [
          'name' => "{$entry}",
          'type' => mime_content_type("{$dir}{$entry}"),
          'size' => filesize("{$dir}{$entry}"),
          'lastmod' => filemtime("{$dir}{$entry}")
        ];
      }
    }
    $d->close();

    return $retval;
  }

  $header = <<<EOT
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <style>
    body {
      font-family: monospace;
    }
    table, th, td {
      border: 1px solid black;
    }
    table {
      border-collapse: collapse;
      margin: 10px auto;
    }
    th, td {
      margin: 0;
      padding: 5px;
    }
  </style>
</head>
<body>
  <table border="1">
    <thead>
      <tr><th>Name</th><th>Type</th><th>Size (Kb)</th></tr>
    </thead>
    <tbody>
EOT;

  $footer = <<<EOT
</body>
</html>
EOT;

  $dirlist = getFileList('.');
  $listingRows = '';
  $serverURL = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/";

  // output file list in HTML TABLE format
  foreach($dirlist as $file) {
    if ($file['type'] == 'text/x-php' || $file['type'] == 'text/html' || $file['type'] == 'text/plain' || $file['type'] == 'inode/x-empty') {
      continue;
    }

    $fh = fopen($file['name'].".html", 'w+') or die ("Can't open file.");

    $imageFileContents = <<<EOT
    <!DOCTYPE html>
    <html lang="en">
    <head>
      <meta charset="UTF-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <meta http-equiv="X-UA-Compatible" content="ie=edge" />
      <meta property="og:image" content="{$serverURL}{$file['name']}" />
      <style>
        html {
          height: 100%;
          position: relative;
        }
        img {
          left: 50%;
          max-width: 98%;
          position: absolute;
          top: 50%;
          transform: translate(-50%, -50%);
        }
      </style>
      <title>{$file['name']}</title>
    </head>
    <body>
      <img src="{$file['name']}" />
    </body>
    </html>
EOT;

    fwrite($fh, $imageFileContents);
    fclose($fh);

    $listingRows .= "<tr>\n";
    $listingRows .= "<td><a href='{$file['name']}.html'>{$file['name']}</a></td>\n";
    $listingRows .= "<td>{$file['type']}</td>\n";
    $listingRows .= "<td>{$file['size']}</td>\n";
    $listingRows .= "</tr>\n";
  }

  $fh = fopen("index.html", 'w+') or die ("Can't open file.");
  fwrite($fh, $header);
  fwrite($fh, $listingRows);
  fwrite($fh, $footer);
  fclose($fh);
?>
