<?php
/**
 * GET /
 */
function main(array $args) : array
{
    return [
        'statusCode' => 301,
        'headers' => [
            'Location' => '/books',
            'Access-Control-Allow-Headers' => 'Content-Type'
        ],
    ];
}
