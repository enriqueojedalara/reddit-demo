{
        paths: {
                'jquery': '../vendor/jquery/2.1.3/jquery.min',
                'bootstrap': '../vendor/bootstrap/3.3.2/js/bootstrap.min',
                'angular': '../vendor/angular/1.3.12/angular.min'
        },
        shim: {
                'bootstrap': {
                        deps: ['jquery']
                },
                'angular': {
                        'exports': 'angular',
                        deps: ['jquery']
                }
        },
        baseUrl: "app",
        name: "main",
        out: "bundle.js",
        removeCombined: true
}