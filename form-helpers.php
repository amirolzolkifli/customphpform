<?php

/**
 * Application Name: Custom PHP Form
 * Application URI: http://github.com/amirolzolkifli/customphpform
 * Description: Custom PHP Form.
 * Version: 1.0.0
 * Author: Amirol Zolkifli
 * Author URI: http://www.amirolzolkifli.com
 * License: MIT
 */

function emailSubject( $name, $subject )
{
	return $name . ', ' . $subject;
}

function emailContent( $data )
{
	return "
<html>
    <head>
        <title></title>
    </head>
    <body>
        <p>Email From: $data[name].</p>
        <p>Email Address: $data[email].</p>
        <p>Phone Number: $data[phone].</p>
        <p>Address: $data[address].</p>
    </body>
</html>
	";
}