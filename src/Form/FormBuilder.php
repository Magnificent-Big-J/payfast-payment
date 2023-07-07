<?php

namespace rainwaves\PayfastPayment\Form;

class FormBuilder
{
    public static function buildForm(array $formFields, string $url): string
    {
        ob_start();
        include 'form_template.php';
        return ob_get_clean();
    }
}