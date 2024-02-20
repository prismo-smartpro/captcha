## Introdução

Sistema de captcha Smart PRO Technology

## Instalação

```bash
composer require prismo-smartpro/captcha
```

## Exemplo de uso

```php
<?php

session_start();

require "vendor/autoload.php";

use SmartPRO\Technology\Captcha;

$captcha = Captcha::Render();

try {
    Captcha::Verify("dm40hg");
}catch (Exception $exception){
    var_dump($exception->getMessage());
}
```