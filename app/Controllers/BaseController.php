<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

use App\Models\Auth\RememberTokenModel;

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 *     class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */

    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Load here all helpers you want to be available in your controllers that extend BaseController.
        // Caution: Do not put the this below the parent::initController() call below.
        // $this->helpers = ['form', 'url'];
        $this->helpers = ['form', 'url', 'theme'];
        // Caution: Do not edit this line.
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        // $this->session = service('session');

        // COOKIES for login applicant
        if (!session()->get('logged_in') && isset($_COOKIE['remember_token'])) {

            $token = $_COOKIE['remember_token'];

            $rememberModel = new RememberTokenModel();

            $record = $rememberModel
                ->where('token', hash('sha256', $token))
                ->where('expires_at >=', date('Y-m-d H:i:s'))
                ->first();

            if ($record) {
                $db = \Config\Database::connect();

                $user = $db->table('applicants a')
                    ->join('users u', 'a.user_id = u.user_id')
                    ->join('programs p', 'a.program_id = p.program_id')
                    ->where('u.user_id', $record['user_id'])
                    ->get()
                    ->getRowArray();

                if ($user) {
                    session()->set([
                        'user_id'          => $user['user_id'],
                        'firstname'        => $user['firstname'],
                        'lastname'         => $user['lastname'],
                        'email'            => $user['email'],
                        'role'             => $user['role'],
                        'applicant_number' => $user['applicant_number'],
                        'applicant_id'     => $user['applicant_id'],
                        'program_id'       => $user['program_id'],
                        'program_name'     => $user['program_name'],
                        'logged_in'        => true
                    ]);
                }
            }
        }
    }

    protected function renderApplicant($page, $data = [])
    {
        $themeModel = new \App\Models\ThemeModel();
        $data['theme'] = $themeModel->getAllSettings();

        return view('applicant/main', array_merge($data, [
            'page_content' => $page
        ]));
    }
}
