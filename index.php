<?php //Main Application access point
require_once $_SERVER['DOCUMENT_ROOT']."/xataface/dataface-public-api.php";
df_init(__FILE__, "/xataface");
$app =& Dataface_Application::getInstance();
$app->display();
