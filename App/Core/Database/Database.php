<?php


namespace App\Core\Database;

use PDO;
use PDOException;

class Database
{
    private static array $config = [
        'HOST' => '',
        'DATABASE' => '',
        'USERNAME' => '',
        'PASSWORD' => '',
    ];

    private static ?PDO $pdo = null;


    /**
     * Init PDO config details for connection
     * @param array $config
     */
    public static function init( array $config )
    {
        self::$config = $config;
    }


    /**
     * Returns a PDO instance
     * @return PDO|null
     */
    public static function instance(): ?PDO
    {
        if ( is_null( self::$pdo ) ) {
            return self::createInstance();
        }
        return self::$pdo;
    }


    /**
     * Create a new PDO instance if one doesnt exist already
     * @return PDO|null
     */
    private static function createInstance(): ?PDO
    {
        try {
            $dsn = sprintf( "mysql:host=%s;dbname=%s", self::$config['HOST'], self::$config['DATABASE'] );
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_CLASS
            ];
            self::$pdo = new PDO( $dsn, self::$config['USERNAME'], self::$config['PASSWORD'], $options );

            return self::$pdo;

        } catch ( PDOException $exception ) {
            die( $exception->getMessage() );
        }
    }


    /**
     * Find all the records form given table and return as given array of objects.
     *
     * @param string $table
     * @param int $limit
     * @param int $offset
     * @param $returnType
     * @param $orderColumn
     * @param string $order
     * @return array
     */
    public static function findAll( string $table, int $limit, int $offset, $returnType, $orderColumn, $order = 'ASC' ): array
    {

        self::instance();
        $statement = self::$pdo->prepare( "select * from {$table} order by {$orderColumn} {$order} limit :limit_val offset :offset_val" );

        $statement->bindValue( ":limit_val", $limit, PDO::PARAM_INT );
        $statement->bindValue( ":offset_val", $offset, PDO::PARAM_INT );
        $statement->execute();

        $result = $statement->fetchAll( PDO::FETCH_CLASS, $returnType );

        if ( !empty( $result ) ) return $result;
        return [];
    }

    /**
     * Find a single instance from the table by id.
     * @param string $table
     * @param int $id
     * @param $returnType
     * @return mixed|null
     */
    public static function find( string $table, int $id, $returnType )
    {
        self::instance();
        $statement = self::$pdo->prepare( "select * from {$table} where id=?" );
        $statement->execute( [ $id ] );

        $result = $statement->fetchObject( $returnType );

        if ( !empty( $result ) ) return $result;
        return null;
    }


    /**
     * Insert a new record and returns id on success
     * @param string $table
     * @param $data
     * @return int
     */
    public static function insert( string $table, $data ): int
    {
        self::instance();

        $query = "insert into {$table} (";

        $columns = array_keys( $data );
        $columnsCount = count( $columns );

        $index = 0;
        foreach ( $columns as $column ) {
            if ( ++$index != $columnsCount ) {
                $query .= $column . ", ";
            } else {
                $query .= $column;
            }
        }


        $query .= ") values(";

        $index = 0;
        foreach ( $columns as $placeholder ) {
            if ( ++$index != $columnsCount ) {
                $query .= ":{$placeholder}, ";
            } else {
                $query .= ":{$placeholder}";
            }
        }

        $query .= ");";


        $statement = self::$pdo->prepare( $query );

        foreach ( $data as $key => $value ) {
            $statement->bindValue( ":{$key}", $value );
        }

        $result = $statement->execute();

        if ( $result ) return (int)self::$pdo->lastInsertId();
        return -1;

    }


    /**
     * Updates the row from given table by unique field
     * @param string $table
     * @param array $data
     * @param array $unique - ['id' => 12]
     * @return bool
     */
    public static function update( string $table, array $data, array $unique )
    {

        self::instance();

        $query = "update {$table} set ";

        $columns = array_keys( $data );
        $columnCount = count( $data );

        $index = 0;
        foreach ( $columns as $column ) {

            if ( ++$index != $columnCount ) {
                $query .= "{$column} = :{$column}, ";
            } else {
                $query .= "{$column} = :{$column} ";
            }
        }

        $uniqueKey = array_keys( $unique )[0];

        $query .= "where {$uniqueKey} = :{$uniqueKey};";

        $statement = self::$pdo->prepare( $query );

        foreach ( $data as $key => $value ) {
            $statement->bindValue( ":{$key}", $value );
        }

        $statement->bindValue( ":{$uniqueKey}", $unique[ $uniqueKey ] );

        return $statement->execute();

    }


    /**
     * Deletes a row from given table where column = value
     * @param string $table
     * @param string $column
     * @param $value
     * @return bool
     */
    public static function delete( string $table, string $column, $value )
    {
        self::instance();

        $query = "delete from {$table} where {$column} = :{$column}";

        $statement = self::$pdo->prepare( $query );
        $statement->bindValue( ":{$column}", $value );

        return $statement->execute();

    }
}
