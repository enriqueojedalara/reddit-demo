/**
* Global configurations 
*/
var CLIENT_ID = 'reddit:0.1.0:web';
var require = {

	waitSeconds: 30,
	paths: {
		'jquery': [
			'https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min',
		],

		'bootstrap': [
			'//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min',
		],

		'angular': [
			'//ajax.googleapis.com/ajax/libs/angularjs/1.4.7/angular.min',
		],

		'angular-resource': [
			'//ajax.googleapis.com/ajax/libs/angularjs/1.4.7/angular-resource.min',
		],

		'angular-cookies': [
			'//ajax.googleapis.com/ajax/libs/angularjs/1.4.7/angular-cookies.min',
		],

		'angular-couch-potato': [
			'http://laurelnaiad.github.io/angular-couch-potato/angular-couch-potato',
		],

		'angular-ui-router': [
			'//cdnjs.cloudflare.com/ajax/libs/angular-ui-router/0.2.15/angular-ui-router.min',
		],

		/*Custom (Hacked)*/
		'domReady': '../vendor/requirejs-domready/2.0.1/domready',
		'lodash': '../vendor/lodash/lodash.min',
        
	},

	shim: {
		'bootstrap':{deps: ['jquery']},
		'angular': {'exports': 'angular', deps: ['jquery']},
		'angular-sanitize': { deps: ['angular'] },
		'angular-bootstrap': { deps: ['angular'] },
		'angular-cookies': { deps: ['angular'] },
		'angular-resource': { deps: ['angular'] },
		'angular-ui-router': { deps: ['angular'] },
		'angular-couch-potato': { deps: ['angular'] },
	},

	priority: [
		'jquery',
		'bootstrap',
		'angular'
	]
};
