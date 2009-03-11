<?php

class TemplatePhp extends Base
{
    public function fetch($templateFile,$var,$charset,$varPrefix) {
        extract($var, empty($varPrefix)? EXTR_OVERWRITE : EXTR_PREFIX_ALL,$varPrefix);
        include $templateFile;
    }
}
?>