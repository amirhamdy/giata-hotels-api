<?php

return [
    'username' => env('GIATA_USERNAME'),
    'password' => env('GIATA_PASSWORD'),
    'timeout' => env('GIATA_TIMEOUT'),

    'ghgml' => [
        'imagesUrl' => 'http://ghgml.giatamedia.com/webservice/rest/1.0/images/',
        'textUrl' => 'http://ghgml.giatamedia.com/webservice/rest/1.0/texts/',
        'providerUrl' => 'http://ghgml.giatamedia.com/webservice/rest/1.0/mappings/',
    ],
    'multicodes' => [
        'countryUrl' => 'https://multicodes.giatamedia.com/webservice/rest/1.latest/properties/country/',
        'providerUrl' => 'https://multicodes.giatamedia.com/webservice/rest/1.latest/properties/gds/',
        'countryMultiUrl' => 'https://multicodes.giatamedia.com/webservice/rest/1.latest/properties/multi/country/',
        'giataIdUrl' => 'https://multicodes.giatamedia.com/webservice/rest/1.latest/properties/',
    ],
];
