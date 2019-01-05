<?php
namespace cafetapi;

/**
 *
 * @author damie
 *        
 */
class ErrorPageBuilder
{
    private $http_error;
    private $error_group;
    private $def;
    private $message;

    /**
     */
    public function __construct(string $http_error)
    {
        $this->http_error = $http_error;
        
        $cafet_errors = cafet_get_errors_info();
        foreach ($cafet_errors as $errorgroup => $cafet_error) if (in_array($http_error, array_keys($cafet_error))) {
            $this->error_group = $errorgroup;
            $this->def = $cafet_error['def'];
            $this->message = $cafet_error[$http_error];
        }
    }
    
    private function getHeaders() : array
    {
        return [];
    }
    
    private function getTitle() : string
    {
        return "$this->http_error Error : $this->message";
    }
    
    private function getStyles() : string
    {
        return <<< EOCSS
html {
    width: 100%;
    height: 100%;
}
body {
    margin: 0;
    width: 100%;
    height: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
}
div.error {
    margin: 0;
    padding-bottom: 3vh;
    color: #686f73;
    text-align: center;
    font-family: HelveticaNeue-Light, sans-serif;
}
div.error img {
    height: 150px;
}
h2 {
    display: block;
    font-size: 1.5em;
    margin-block-start: 0.83em;
    margin-block-end: 0.83em;
    margin-inline-start: 0px;
    margin-inline-end: 0px;
    font-weight: bold;
}
p {
    color: #838789;
}
EOCSS;
    }
    
    private function getHead() : string
    {
        $title = $this->getTitle();
        $css = $this->getStyles();
        return <<< EOHTML
<head>
	<title>$title</title>
	<meta charset="UTF-8">
    <style>
$css
    </style>
</head>
EOHTML;
    }

    private function getBody() : string
    {
        $logo_uri = 'http://cafet/assets/logo.png';
        return <<< EOHTML
<body>
	<div class="error">
        <img src="$logo_uri" alt="Logo" />
        <h2>$this->http_error Error</h2>
        <p>An error occured: <strong>$this->message</strong>, ($this->def)</p>
        <hr />
        <p>Please contact an administrator</p>
    </div>
</body>
EOHTML;
    }
    
    public function print()
    {
        header("HTTP/2 $this->http_error $this->message");
        foreach($this->getHeaders() as $name => $content) header("$name: $content");
        
        $head = $this->getHead();
        $body = $this->getBody();
        
        print <<< EOHTML
<!DOCTYPE html>
<html>
$head
$body
</html>
EOHTML;
        exit();
    }
}

