<?php

/******************* Configuration file **************************/

/**
 * Globale
 */
const VAR_DIR = '/var/www/gfaim/backend/var';

/**
 * SearchEngineExtraction Configuration
 */
const SEARCH_ENGINE_URL = 'https://www.googleapis.com/customsearch/v1';
const GOOGLE_SEARCH_CX  = '014001075900266475386:idgzvgxto1s';

const SEARCH_ENGINE_KEYS = array(
    0 => 'AIzaSyASK1nR0p8Mjx4Fw9XHvMI-m20xSbtAwnc',
    1 => 'AIzaSyCPZQ6hboLZx-6rx9sRTh1e7mzzfXwK-fg',
    2 => 'AIzaSyBuS4S5W8vAkRajhQuy_Hv2CM6JPvfLbPo',
    3 => 'AIzaSyAiLHN42sSVAg418iQ5S81SLqkaFgPY8rk'
);

const NUMBER_OF_SEARCH_ENGINE_KEYS = 4;

/**
 * TextAnnotation Configuration
 */
//define('SPOTLIGHT_URL',         'http://spotlight.dbpedia.org/rest/annotate');
const SPOTLIGHT_URL = 'http://spotlight.gfaim.antoine-chabert.fr/rest/annotate';
const SPOTLIGHT_CONFIDENCE = 0;

/**
 * Cache DataBase
 */
const DATABASE_ROOT = VAR_DIR . '/cache.sqlite3';