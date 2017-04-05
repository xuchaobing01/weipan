<?php
include_once './IPSPay.class.php';

$IPSPay = new IPSPay('001', '1.00', date('Ymd'), 'office');

$form = $IPSPay->form();

echo $form;
