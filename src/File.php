<?php

declare( strict_types = 1 );

namespace Ocolin\EasySwagger;

/**
 *  This is a root class that containts the raw Swagger file data so that
 * it can be accessed by all the various classes
 */


abstract class File
{
    protected static object $file;


/*
---------------------------------------------------------------------------- */

    public static function error( string $msg ) : void
    {
        fwrite( stream: STDERR, data: $msg . "\n" );
    }
}