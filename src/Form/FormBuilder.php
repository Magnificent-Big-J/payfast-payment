<?php

namespace rainwaves\PayfastPayment\Form;

class FormBuilder
{
    public static function buildForm(array $formFields, string $url, bool $autoSubmit = false): string
    {
        ob_start();
        include __DIR__ . '/form_template.php';
        return ob_get_clean();
    }
}
