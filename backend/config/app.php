<?php

/******************* Configuration file **************************/

/**
 * SearchEngineExtraction Configuration
 */
define('SEACH_ENGINE',          'google'); // google (todo duckduckgo / yahoo)
define('SEARCH_ENGINE_URL',     'https://www.googleapis.com/customsearch/v1');
define('SEARCH_ENGINE_KEY',     'AIzaSyASK1nR0p8Mjx4Fw9XHvMI-m20xSbtAwnc');
define('GOOGLE_SEARCH_CX',      '014001075900266475386:idgzvgxto1s');

define('SEARCH_ENGINE_KEY2',     'AIzaSyCPZQ6hboLZx-6rx9sRTh1e7mzzfXwK-fg');
define('SEARCH_ENGINE_KEY3',     'AIzaSyBuS4S5W8vAkRajhQuy_Hv2CM6JPvfLbPo');
define('SEARCH_ENGINE_KEY4',     'AIzaSyAiLHN42sSVAg418iQ5S81SLqkaFgPY8rk');


/**
 * TextAnnotation Configuration
 */
//define('SPOTLIGHT_URL',         'http://spotlight.dbpedia.org/rest/annotate');
define('SPOTLIGHT_URL',         'http://spotlight.gfaim.antoine-chabert.fr/rest/annotate');
define('SPOTLIGHT_CONFIDENCE',  0);

/**
 * Cache DataBase
 */
define('DATABASE_ROOT', '/var/www/gfaim/backend/var/cache.sqlite');