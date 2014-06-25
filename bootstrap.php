<?php
if (!defined('APP_ROOT')) {
    define('APP_ROOT', __DIR__ . '/');
}

// Include the composer stuff
require APP_ROOT . 'vendor/autoload.php';

$app = new Silex\Application();

$app['db'] = new \PDO(
    'sqlite:' . APP_ROOT . 'LVAC.sqlite'
);
$app['db']->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

$app->register(
    new Silex\Provider\TwigServiceProvider(),
    array(
        'twig.path' => APP_ROOT . 'templates',
        'debug' => true
    )
);
$app->register(new Silex\Provider\SessionServiceProvider());

$app->mount('/', new LVAC\BaseControllerProvider());
$app->mount('/news', new LVAC\NewsControllerProvider());
$app->mount('/members', new LVAC\MembersControllerProvider());

$app->get('/login', function () use ($app) {
    return $app['twig']->render('/login.html');
});
$app->post('/login', function () use ($app) {
    if (!isset($request->get['email']) || !isset($request->get['password'])) {
        $error = "You have to fill in your email and password";
        return $app['twig']->render('/login.html', array('error' => $error));
    }
    $email = $request->get['email'];
    $password = $request->get['password'];

    $member_mapper = new \LVAC\MemberMapper($app['db']);
    if (false === $member = $member_mapper->check_login($email, $password)) {
        // throw some errors
        $error = "The username or password was incorrect";
        return $app['twig']->render('/login.html', array('error' => $error, 'email' => $email));
    }
    $app['session']->set('member', array('email' => $email));
    return $app->redirect('/members');
});

return $app;
