<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');
Yii::setPathOfAlias('bootstrap', dirname(__FILE__).'/../extensions/bootstrap');
// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Мой блог сайт',
    'theme'=>'bootstrap', //подключение темы

    'modules'=>array(
        'gii'=>array(
            'generatorPaths'=>array(
                'bootstrap.gii',
            ),
        ),
    ),

    'language'=>'ru',

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
    'import'=>array(
        'application.models.*',
        'application.components.*',
        'application.helpers.*',
        'ext.eoauth.*',
        'ext.eoauth.lib.*',
        'ext.lightopenid.*',
        'ext.eauth.*',
        'ext.eauth.services.*',
    ),

	'modules'=>array(

        'admin',



		'gii'=>array(
            'generatorPaths'=>array(
                'bootstrap.gii',
            ),
        ),
        'gii'=>array(
            'class'=>'system.gii.GiiModule',
            'password'=>'123',
            // If removed, Gii defaults to localhost only. Edit carefully to taste.
            'ipFilters'=>array('127.0.0.1','::1'),
        ),
	),

	// application components
	'components'=>array(

        'loid' => array(
            'class' => 'ext.lightopenid.loid',
        ),

        'eauth' => array(
            'class' => 'ext.eauth.EAuth',
            'popup' => true, // Use the popup window instead of redirecting.
            'cache' => false, // Cache component name or false to disable cache. Defaults to 'cache'.
            'cacheExpire' => 0, // Cache lifetime. Defaults to 0 - means unlimited.
            'services' => array( // You can change the providers and their classes.
                'facebook' => array(
                    // register your app here: https://developers.facebook.com/apps/
                    'class' => 'FacebookOAuthService',
                    'client_id' => '117583471750986',
                    'client_secret' => '8f358e01b8815525de4eb04531f80f87',
                ),
                'vkontakte' => array(
                    // register your app here: https://vk.com/editapp?act=create&site=1
                    'class' => 'VKontakteOAuthService',
                    'client_id' => '3367128',
                    'client_secret' => 'vg9U89YnPKoEx054OYp6',
                ),
            ),
        ),

        'image'=>array(
            'class'=>'application.extensions.image.CImageComponent',
            // GD or ImageMagick
            'driver'=>'GD',
            // ImageMagick setup path
            'params'=>array('directory'=>'/images/'),
        ),


        'mailer' => array(
            'class' => 'application.extensions.mailer.EMailer',
            'CharSet' => 'UTF-8',
            'ContentType' => 'text/html',
        ),



        'authManager' => array(
            // Будем использовать свой менеджер авторизации
            'class' => 'PhpAuthManager',
            // Роль по умолчанию. Все, кто не админы, модераторы и юзеры — гости.
            'defaultRoles' => array('guest'),
        ),
	
	'bootstrap'=>array(
            'class'=>'bootstrap.components.Bootstrap',
        ),
		
		'user'=>array(
            'class' => 'WebUser',
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
		),



		// uncomment the following to enable URLs in path-format

   	'urlManager'=>array(
			'urlFormat' => 'path',
			'showScriptName'=>false,
			'urlSuffix' => '',
			'rules'=> array_merge(
				array(
                    'feed.xml' => array('post/feed', 'urlSuffix' => ''),
					'<controller:\w+>/<id:\d+>'=>'<controller>/index',
					'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
					'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
				)
			),
		),

		 'db'=>array(
            'connectionString' => 'mysql:host=localhost;dbname=yiicms',
            'emulatePrepare' => true,
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
        ),

        'errorHandler'=>array(
            // use 'site/error' action to display errors
            'errorAction'=>'site/error',
        ),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
				// uncomment the following to show log messages on web pages
				/*
				array(
					'class'=>'CWebLogRoute',
				),
				*/
			),
		),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'webmaster@example.com',
	),
);