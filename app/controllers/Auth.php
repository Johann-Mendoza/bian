<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

// Custom logging function to write logs to a file
function custom_log($message) {
    $log_file = ROOT_DIR . '/logs/debug_log.txt';  // Change this path if necessary
    file_put_contents($log_file, date('Y-m-d H:i:s') . ' - ' . $message . PHP_EOL, FILE_APPEND);
}

class Auth extends Controller {

    private $logger;

    public function __construct()
    {
        parent::__construct();
        $this->logger = new Logger();
        if(segment(2) != 'logout') {
            if(logged_in()) {
                redirect('home');
            }
        }
        $this->call->library('email');
    }
	
    public function index() {
        $this->call->view('auth/login');
    }  

    public function login() {
        if ($this->form_validation->submitted()) {
            $email = $this->io->post('email');
            $password = $this->io->post('password');
            $data = $this->lauth->login($email, $password); // Login attempt
    
            if (empty($data)) {
                // Failed login
                $this->session->set_flashdata(['is_invalid' => 'is-invalid']);
                $this->session->set_flashdata(['err_message' => 'These credentials do not match our records.']);
                $this->logger->log('error', 'Login Failure', 'Invalid credentials for email: ' . $email, __FILE__, __LINE__);
            } else {
                // Successful login
                $this->lauth->set_logged_in($data);  // Set user session data
    
                // Assuming get_role() function returns the role (admin/client)
                $role = $this->lauth->get_role($data);  // Get the role from the database or session
                
                // Log the role (optional for debugging)
                $this->logger->log('debug', 'User Role', 'User role: ' . $role, __FILE__, __LINE__);
    
                // Redirect based on the user's role
                if ($role === 'admin') {
                    redirect('home');  // Redirect to admin dashboard
                } else {
                    redirect('clientdashboard');  // Redirect to client dashboard
                }
            }
            redirect('auth/login');  // In case of any issue, stay on the login page
        } else {
            $this->call->view('auth/login');  // Load the login view
        }
    }
    


    
    

    public function register() {
        if($this->form_validation->submitted()) {
            $username = $this->io->post('username');
            $email = $this->io->post('email');
            $email_token = bin2hex(random_bytes(50));
            $this->form_validation
                ->name('username')
                    ->required()
                    ->is_unique('users', 'username', $username, 'Username was already taken.')
                    ->min_length(5, 'Username must not be less than 5 characters.')
                    ->max_length(20, 'Username must not be more than 20 characters.')
                    ->alpha_numeric_dash('Special characters are not allowed in username.')
                ->name('password')
                    ->required()
                ->name('password_confirmation')
                    ->required()
                    ->matches('password', 'Passwords did not match.')
                ->name('email')
                    ->required()
                    ->is_unique('users', 'email', $email, 'Email was already taken.');
            
            if($this->form_validation->run()) {
                // Include default role as 'client'
                if($this->lauth->register($username, $email, $this->io->post('password'), $email_token, 'client')) {
                    $data = $this->lauth->login($email, $this->io->post('password'));
                    $this->lauth->set_logged_in($data);
                    $this->session->set_userdata('role', 'client'); // Default role
                    redirect('clientdashboard.php'); // Redirect new clients to their dashboard
                } else {
                    set_flash_alert('danger', config_item('SQLError'));
                }
            } else {
                set_flash_alert('danger', $this->form_validation->errors()); 
                redirect('auth/register');
            }
        } else {
            $this->call->view('auth/register');
        }
    }

    private function send_password_token_to_email($email, $token) {
		$template = file_get_contents(ROOT_DIR.PUBLIC_DIR.'/templates/reset_password_email.html');
		$search = array('{token}', '{base_url}');
		$replace = array($token, base_url());
		$template = str_replace($search, $replace, $template);
		$this->email->recipient($email);
		$this->email->subject('LavaLust Reset Password'); //change based on subject
		$this->email->sender('joannemile2003@gmail.com'); //change based on sender email
		$this->email->reply_to('joannemile2003@gmail.com'); // change based on sender email
		$this->email->email_content($template, 'html');
		$this->email->send();
	}

	public function password_reset() {
		if($this->form_validation->submitted()) {
			$email = $this->io->post('email');
			$this->form_validation
				->name('email')->required()->valid_email();
			if($this->form_validation->run()) {
				if($token = $this->lauth->reset_password($email)) {
					$this->send_password_token_to_email($email, $token);
                    $this->session->set_flashdata(['alert' => 'is-valid']);
				} else {
					$this->session->set_flashdata(['alert' => 'is-invalid']);
				}
			} else {
				set_flash_alert('danger', $this->form_validation->errors());
			}
		}
		$this->call->view('auth/password_reset');
	}

    public function set_new_password() {
        if($this->form_validation->submitted()) {
            $token = $this->io->post('token');
			if(isset($token) && !empty($token)) {
				$password = $this->io->post('password');
				$this->form_validation
					->name('password')
						->required()
						->min_length(8, 'New password must be atleast 8 characters.')
					->name('re_password')
						->required()
						->min_length(8, 'Retype password must be atleast 8 characters.')
						->matches('password', 'Passwords did not matched.');
						if($this->form_validation->run()) {
							if($this->lauth->reset_password_now($token, $password)) {
								set_flash_alert('success', 'Password was successfully updated.');
							} else {
								set_flash_alert('danger', config_item('SQLError'));
							}
						} else {
							set_flash_alert('danger', $this->form_validation->errors());
						}
			} else {
				set_flash_alert('danger', 'Reset token is missing.');
			}
    	redirect('auth/set-new-password/?token='.$token);
        } else {
             $token = $_GET['token'] ?? '';
            if(! $this->lauth->get_reset_password_token($token) && (! empty($token) || ! isset($token))) {
                set_flash_alert('danger', 'Invalid password reset token.');
            }
            $this->call->view('auth/new_password');
        }
		
	}

    public function logout() {
        if($this->lauth->set_logged_out()) {
            $this->session->unset_userdata('role'); // Clear role
            redirect('auth/login');
        }
    }

}
?>
