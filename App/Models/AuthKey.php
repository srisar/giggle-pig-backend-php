<?php


namespace App\Models;


use App\Core\Database\Database;
use Carbon\Carbon;
use Exception;

class AuthKey
{
    private const TABLE = "users_auth_keys";

    public ?int $user_id;
    public ?string $auth_key, $valid_till;

    public ?User $user;

    /**
     * @param User $user
     * @return string
     * @throws Exception
     */
    public static function generateAuthKey( User $user ): string
    {

        /* check if user exist, if not there is no point generating the key */
        if ( is_null( User::find( $user->id ) ) ) throw new Exception( "User does not exist" );

        /*
         * check if there is an auth key already in the database, if there is one,
         * check if it is still valid
         * */
        $authKey = self::getAuthKey( $user );
        if ( !is_null( $authKey ) ) {

            $now = Carbon::now();
            $keyValidTill = new Carbon( $authKey->valid_till );

            if ( $now->greaterThan( $keyValidTill ) ) {
                // key is expired

                if ( !self::deleteAuthKey( $user ) ) {
                    throw new Exception( "Failed to delete existing auth key" );
                }

            } else {
                return $authKey->auth_key;
            }

        }

        /*
         * There is no valid auth key in the database,
         * generate one and return it
         * */
        $key = md5( microtime() . rand() );
        $validTill = Carbon::now()->add( 1, "day" )->toDateTimeString();

        $db = Database::instance();
        $s = $db->prepare( "insert into users_auth_keys(user_id, auth_key, valid_till) values (?,?, ?)" );

        $result = $s->execute( [ $user->id, $key, $validTill ] );

        if ( $result ) {
            return $key;
        } else {
            throw new Exception( "Failed to generate auth key" );
        }

    }


    /**
     * Check if the key exist and valid (not expired), if valid,
     * instance of AuthKey with user will be returned
     * @param string $authKey
     * @return self
     */
    public static function validateAuthKey( string $authKey ): ?AuthKey
    {

        $db = Database::instance();
        $statement = $db->prepare( "select * from users_auth_keys where auth_key=?" );

        $statement->execute( [ $authKey ] );

        /** @var AuthKey $result */
        $result = $statement->fetchObject( self::class );

        if ( !empty( $result ) ) {

            $now = Carbon::now();
            $validTill = new Carbon( $result->valid_till );

            if ( $now->greaterThan( $validTill ) ) {
                return null;
            }

            $result->user = User::find( $result->user_id );
            return $result;

        }
        return null;

    }

    /**
     * @param User $user
     * @return AuthKey|null
     */
    private static function getAuthKey( User $user ): ?AuthKey
    {

        $db = Database::instance();
        $statement = $db->prepare( "select * from users_auth_keys where user_id=?" );
        $statement->execute( [ $user->id ] );

        /** @var AuthKey $result */
        $result = $statement->fetchObject( self::class );

        if ( !empty( $result ) ) {

            return $result;

        }
        return null;

    }

    /**
     * Delete the existing auth key for the user
     * @param User $user
     * @return bool
     */
    private static function deleteAuthKey( User $user ): bool
    {
        return Database::delete( self::TABLE, "user_id", $user->id );
    }
}
