<?php return array (
  'anlutro/l4-settings' => 
  array (
    'aliases' => 
    array (
      'Setting' => 'anlutro\\LaravelSettings\\Facade',
    ),
    'providers' => 
    array (
      0 => 'anlutro\\LaravelSettings\\ServiceProvider',
    ),
  ),
  'barryvdh/laravel-debugbar' => 
  array (
    'providers' => 
    array (
      0 => 'Barryvdh\\Debugbar\\ServiceProvider',
    ),
    'aliases' => 
    array (
      'Debugbar' => 'Barryvdh\\Debugbar\\Facades\\Debugbar',
    ),
  ),
  'eklundkristoffer/seedster' => 
  array (
    'providers' => 
    array (
      0 => 'Seedster\\SeedsterServiceProvider',
    ),
  ),
  'fideloper/proxy' => 
  array (
    'providers' => 
    array (
      0 => 'Fideloper\\Proxy\\TrustedProxyServiceProvider',
    ),
  ),
  'hexters/coinpayment' => 
  array (
    'providers' => 
    array (
      0 => 'Hexters\\CoinPayment\\Providers\\CoinPaymentServiceProvider',
    ),
    'aliases' => 
    array (
      'CoinPayment' => 'Hexters\\CoinPayment\\Helpers\\CoinPaymentFacade',
    ),
  ),
  'intergo/sms.to-laravel-lumen' => 
  array (
    'providers' => 
    array (
      0 => 'Intergo\\SmsTo\\ServiceProvider',
    ),
    'aliases' => 
    array (
      'SmsTo' => 'Intergo\\SmsTo\\Facades\\SmsToFacade',
    ),
  ),
  'intervention/image' => 
  array (
    'providers' => 
    array (
      0 => 'Intervention\\Image\\ImageServiceProvider',
    ),
    'aliases' => 
    array (
      'Image' => 'Intervention\\Image\\Facades\\Image',
    ),
  ),
  'jenssegers/agent' => 
  array (
    'providers' => 
    array (
      0 => 'Jenssegers\\Agent\\AgentServiceProvider',
    ),
    'aliases' => 
    array (
      'Agent' => 'Jenssegers\\Agent\\Facades\\Agent',
    ),
  ),
  'jeremykenedy/laravel-roles' => 
  array (
    'providers' => 
    array (
      0 => 'jeremykenedy\\LaravelRoles\\RolesServiceProvider',
    ),
  ),
  'laravel/legacy-factories' => 
  array (
    'providers' => 
    array (
      0 => 'Illuminate\\Database\\Eloquent\\LegacyFactoryServiceProvider',
    ),
  ),
  'laravel/tinker' => 
  array (
    'providers' => 
    array (
      0 => 'Laravel\\Tinker\\TinkerServiceProvider',
    ),
  ),
  'laravelcollective/html' => 
  array (
    'providers' => 
    array (
      0 => 'Collective\\Html\\HtmlServiceProvider',
    ),
    'aliases' => 
    array (
      'Form' => 'Collective\\Html\\FormFacade',
      'Html' => 'Collective\\Html\\HtmlFacade',
    ),
  ),
  'nesbot/carbon' => 
  array (
    'providers' => 
    array (
      0 => 'Carbon\\Laravel\\ServiceProvider',
    ),
  ),
  'nunomaduro/collision' => 
  array (
    'providers' => 
    array (
      0 => 'NunoMaduro\\Collision\\Adapters\\Laravel\\CollisionServiceProvider',
    ),
  ),
  'pragmarx/google2fa-laravel' => 
  array (
    'providers' => 
    array (
      0 => 'PragmaRX\\Google2FALaravel\\ServiceProvider',
    ),
    'aliases' => 
    array (
      'Google2FA' => 'PragmaRX\\Google2FALaravel\\Facade',
    ),
  ),
  'proengsoft/laravel-jsvalidation' => 
  array (
    'providers' => 
    array (
      0 => 'Proengsoft\\JsValidation\\JsValidationServiceProvider',
    ),
    'aliases' => 
    array (
      'JsValidator' => 'Proengsoft\\JsValidation\\Facades\\JsValidatorFacade',
    ),
  ),
  'tymon/jwt-auth' => 
  array (
    'aliases' => 
    array (
      'JWTAuth' => 'Tymon\\JWTAuth\\Facades\\JWTAuth',
      'JWTFactory' => 'Tymon\\JWTAuth\\Facades\\JWTFactory',
    ),
    'providers' => 
    array (
      0 => 'Tymon\\JWTAuth\\Providers\\LaravelServiceProvider',
    ),
  ),
  'yajra/laravel-datatables-oracle' => 
  array (
    'providers' => 
    array (
      0 => 'Yajra\\DataTables\\DataTablesServiceProvider',
    ),
    'aliases' => 
    array (
      'DataTables' => 'Yajra\\DataTables\\Facades\\DataTables',
    ),
  ),
);