<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

define('DIAS','11');
define('PAGTO','s');
define('PRECO_CADASTRO','n');
define('TUTORIAL','s');

define('TITLE_PAGE','TilTheDay');
define('APP_ITUNES','tiltheday');
define('CAMINHO_SITE','www.tiltheday.com');
define('EMAIL_HOST','mail.tiltheday.com');
define('EMAIL_CONTATO','contato@tiltheday.com');
define('EMAIL_CONTATO_SENHA','dudinha09');
define('EMAIL_NOREPLY','noreply@tiltheday.com');
define('EMAIL_NOREPLY_SENHA','dudinha09');
define('EMAIL_INVITE','invite@tiltheday.com');
define('EMAIL_INVITE_SENHA','dudinha09');
define('INSTAGRAM_ID','4df5f47cf2fa4da98b0d0f91beb158fb');
define('INSTAGRAM_SECRET','4664a8ef7e3142e4bb12fb19fd4aa4d3');
define('INSTAGRAM_REDIRECT','http://www.tiltheday.com/auth/token');
define('FACEBOOK_ID','445876232159922');
define('FACEBOOK_SECRET','4e8db7c42234a9eac60854309e35a986');
define('FACEBOOK_REDIRECT','http://www.tiltheday.com/auth/token_facebook');
define('FACEBOOK_TOKEN','');

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ', 'rb');
define('FOPEN_READ_WRITE', 'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE', 'ab');
define('FOPEN_READ_WRITE_CREATE', 'a+b');
define('FOPEN_WRITE_CREATE_STRICT', 'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');


/* End of file constants.php */
/* Location: ./application/config/constants.php */