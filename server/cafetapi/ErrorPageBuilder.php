<?php
namespace cafetapi;

use cafetapi\config\Config;

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
        
        foreach (Kernel::errorsInfo() as $errorgroup => $cafet_error) if (in_array($http_error, array_keys($cafet_error))) {
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
        ob_start();
?>
<style>
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
</style>
<?php
        return ob_get_clean();
    }
    
    private function getHead() : string
    {
        ob_start();
?>
<head>
	<title><?=$this->getTitle()?></title>
	<meta charset="UTF-8">
<?=$this->getStyles()?>
</head>
<?php
        return ob_get_clean();
    }

    private function getBody() : string
    {
        ob_start();
?>
<body>
	<div class="error">
        <img src="<?=trim(Config::url, '/')?>/assets/logo.png" alt="Logo" />
        <h2><?=$this->http_error?> Error</h2>
        <p>An error occured: <strong><?=$this->message?></strong>, (<?=$this->def?>)</p>
        <hr />
        <p>Please contact an administrator</p>
    </div>
</body>
<?php
        return ob_get_clean();
    }
    
    public function print()
    {
        header("HTTP/2 $this->http_error $this->message");
        foreach($this->getHeaders() as $name => $content) header("$name: $content");
?>
<!DOCTYPE html>
<html>
<?=$this->getHead()?>
<?=$this->getBody()?>
</html>
<?php
        exit();
    }
}

