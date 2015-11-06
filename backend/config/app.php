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

const SEARCH_ENGINE_KEYS_RECIPES = array(
    0 => 'AIzaSyDq3GT6ywCVX2u1c2jJnGNh5y2pqxEJ8CQ',
    1 => 'AIzaSyBWJjMi9ThbZmFRF3HBr-bVlAWoZK5EDTE',
    2 => 'AIzaSyBvQwhdSuMCSWsQd4EX-c3o0kep3e-Ldmk'
);

const SEARCH_ENGINE_KEYS_FOOD = array(
    0 => 'AIzaSyAdQ7sbLYwqbuliGCWCKdU9nEJxIy4_8m8',
    1 => 'AIzaSyAbF4v2-eaUhOehrWYQ1xosEXnisJYJYAs',
    2 => 'AIzaSyBGzkRAaD4Sqmfud_QShUGbcm0P3DZALLk'
);

const NUMBER_OF_SEARCH_ENGINE_KEYS = 3;

// Request with this default number of pages while be cached
const DEFAULT_NUMBER_OF_PAGES = 20;

/**
 * TextExtractor Configuration
 */
const TIMEOUT_EXTRACTION = 3;

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
const WAIT_TIME_UNLOCKED_DATABASE = 30;

/**
 * SPARQL
 */
const GENERAL_INFO_LABEL = 'http://www.w3.org/2000/01/rdf-schema#label';
const GENERAL_INFO_COMMENT = 'http://www.w3.org/2000/01/rdf-schema#comment';
const GENERAL_INFO_IMAGECAPTION = 'http://dbpedia.org/property/imageCaption';
    
const GENERAL_INFO_THUMBNAIL = 'http://dbpedia.org/ontology/thumbnail';
const GENERAL_INFO_PRIMARYTOPIC = 'http://xmlns.com/foaf/0.1/isPrimaryTopicOf';

const PROPERTY_GENUS = 'http://dbpedia.org/ontology/genus';
const PROPERTY_MAININGREDIENTOF = 'http://dbpedia.org/property/mainIngredient';
const PROPERTY_RECIPIE_IMAGE = 'http://xmlns.com/foaf/0.1/depiction';

/**
 * Graph
 */
const GRAPH_DEFAULT_SIMILARITY = 1.5;

/**
 * GFaim Results
 */
const NB_MAX_RECIPIES = 12;