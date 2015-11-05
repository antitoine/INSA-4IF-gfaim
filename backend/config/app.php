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
    3 => 'AIzaSyAiLHN42sSVAg418iQ5S81SLqkaFgPY8rk',
    4 => 'AIzaSyCSPh3AAxZuKLvM5XNc5rS09yWKxXVKa9A',
    5 => 'AIzaSyCRrQOOrNMJLZt-Lhcuvgqm0x-rDXeqkyw',
    6 => 'AIzaSyCzKQkRUD47ZZN9fy4Mhoo5oSWVxRdrccg'
);

const NUMBER_OF_SEARCH_ENGINE_KEYS = 7;

/**
 * TextAnnotation Configuration
 */
//const SPOTLIGHT_URL = 'http://spotlight.dbpedia.org/rest/annotate';
const SPOTLIGHT_URL = 'http://spotlight.gfaim.antoine-chabert.fr/rest/annotate';
const SPOTLIGHT_DEFAULT_CONFIDENCE = 1;

/**
 * Cache DataBase
 */
const CACHE_ENABLED = true;
const DATABASE_ROOT = VAR_DIR . '/cache.sqlite3';


/**
 * SPARQL
 */
const GENERAL_INFO_LABEL = 'http://www.w3.org/2000/01/rdf-schema#label';
const GENERAL_INFO_COMMENT = 'http://www.w3.org/2000/01/rdf-schema#comment';
const GENERAL_INFO_IMAGECAPTION = 'http://dbpedia.org/property/imageCaption';
    
const GENERAL_INFO_THUMBNAIL = 'http://dbpedia.org/ontology/thumbnail';
const GENERAL_INFO_PRIMARYTOPIC = 'http://xmlns.com/foaf/0.1/isPrimaryTopicOf';

const PROPERTY_GENUS = 'http://dbpedia.org/ontology/genus';

/**
 * Graph
 */
const GRAPH_DEFAULT_SIMILARITY = 1.5;