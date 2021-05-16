<?php


namespace App\Extensions;


/**
 * Class Database
 *
 * Connect to DB, get and insert data
 *
 * @package App\Extensions
 */
class Database
{
    /**
     * @var \mysqli mysqli connection
     */
    private static \mysqli $connection;

    /**
     * Database constructor.
     *
     * Set up db connection
     */
    public function __construct()
    {
        if (
            !defined("APP_DB_HOST") ||
            !defined("APP_DB_USERNAME") ||
            !defined("APP_DB_PASSWORD") ||
            !defined("APP_DB")
        ) {
            exit("Can't connect to database!");
        }

        self::$connection = new \mysqli(
            APP_DB_HOST,
            APP_DB_USERNAME,
            APP_DB_PASSWORD,
            APP_DB
        );
    }

    /**
     * Base query for db
     *
     * @param $sql
     * @return bool|\mysqli_result
     */
    public function query($sql)
    {
        return self::$connection->query($sql);
    }

    /**
     * Get last insert ID
     * @return int|string
     */
    public function getInsertID()
    {
        return self::$connection->insert_id;
    }

    /**
     * Escape string by default mysqli method
     * @param $string
     * @return string
     */
    public function real_escape_string($string)
    {
        return self::$connection->real_escape_string($string);
    }

}