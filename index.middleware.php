<?php
error_reporting(E_ALL | E_STRICT);

// 
// Require modules
require 'Slim/Slim.php';
\Slim\Slim::registerAutoloader();

require 'Slim/Log.php';
require 'Middleware/auth.php';

// Get Slim Extras from here https://github.com/codeguy/Slim-Extras
require 'Slim/Extras/Log/DateTimeFileWriter.php';

require 'Lib/Utils.php';
require 'Lib/Acl.php';

//
// Init Slim
$app = new \Slim\Slim(array(
    'debug' => true,
    'log.writer' => new \Slim\Extras\Log\DateTimeFileWriter(array(
        'path' => './logs',
        'name_format' => 'Y-m-d',
        'message_format' => '%label% - %date% - %message%'
    ))
));
$app->add(new \Middleware\Auth\AuthMiddleware(getAcl()));

//
// Routes

// Home
$app->get('/',function () use ($app) {
  $app->log->debug('root');
  endResponse(200, 'OK', 'Welcome to API!', null, $app);
});

// Auth
$app->post('/login', function () use ($app) {
  $body = $app->request->post();

  if (!(isset($body['username']) && isset($body['password']))) {
    endResponse(403, 'Error', 'Required field is missing.', null, $app);
  }

  if ($body['username'] == 'bill' && $body['password'] == 'kill') {
    $data['token'] = 'aMxRfN0TjOc9UzUmG3SgtMvv02E7FhoK';
    endResponse(200, 'OK', 'Login OK', $data, $app);
  }
  else {
    endResponse(403, 'Error', 'Invalid credentials.', null, $app);
  }

});

// Auth-only resources
$app->get('/secret', function () use ($app) {

  $data['secret'] = 'This is super secret information available only to you!!';
  endResponse(200, 'OK', 'Secret is here!!', $data, $app);
});

// Auth-only with Subscription resources
$app->get('/moresecret', function () use ($app) {

  $data['secret'] = 'This is super subscriber secret information available only to you!!';
  endResponse(200, 'OK', 'Subscriber Secret is here!!', $data, $app);
});

//
// Run
$app->run();
