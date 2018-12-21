<?php

namespace App\Middleware;

use App\Models\User;
use MBI\AppleConnect;
use Teapot\Environment;
use Teapot\Interfaces\Gatekeeper;
use Teapot\Interfaces\Middleware;
use Teapot\Interfaces\Registration;
use Teapot\Request;
use Teapot\Visitor;

class Authenticate implements Middleware, Registration, Gatekeeper
{

    protected static $visitor;
    protected static $request;

    public static function execute(Request $request, Visitor $visitor)
    {
        $instance        = new self;
        static::$visitor = $visitor;
        static::$request = $request;

        if (Environment::isLocal()) {
            $_SESSION['_id'] = (int) config('server.dsid');
        } else {
            $instance->_parseACData(AppleConnect::authenticate());
        }

        $instance->_buildFromDatabase();

    }

    protected function _buildFromDatabase()
    {
        if (!isset($_SESSION['_id'])) {
            throw new \Exception('No identifier to configure user.', 418);
        }

        $visitor = &static::$visitor;
        $user    = new User();
        $id      = $_SESSION['_id'];

        $visitor->identify($user->find($id) ?: []);

        if ($visitor->role() === 'Team Manager' || $visitor('is_tma') === true) {
            $visitor->mergeData($user->directReports($id));
        }

        if ($visitor->role() === 'Mentor') {
            $visitor->mergeData($user->assignments($id));
        }
    }

    /**
     * Translates the data as it is received from AppleConnect
     *
     * @return void
     */
    protected function _parseACData(AppleConnect $response)
    {

        if ($response('reason') !== 'Success') {
            static::deny(static::$request);
        }

        $user            = $response->user();
        $_SESSION['_id'] = $user['prsId'];

        static::$visitor->mergeData([
            'first_name'   => $user['nickName'],
            'last_name'    => $user['lastName'],
            'email'        => $user['emailAddress'],
            'ds_id'        => $user['prsId'],
            'country_code' => $user['officialCountryCd'],
            'is_manager'   => $user['isManager'] === 'Y',
        ]);

    }

    /**
     * Used when Authentication fails
     */
    public static function deny(Request $request)
    {
        static::destroy();
        view('system/ac-error');
        die;
    }

    public static function destroy()
    {
        AppleConnect::logout();
    }
}
