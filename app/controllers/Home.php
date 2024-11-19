<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

require_once APP_DIR . 'models/Product_model.php';

class Home extends Controller {

    protected $product_model;

    public function __construct() {
        parent::__construct();
        $this->product_model = new Product_model();
        
        if(! logged_in()) {
            redirect('auth');
        }
    }

	public function index() {

  
        $data['products'] = $this->product_model->get_all_products();
       
        $data['title'] = "Product List - LavaLust Framework";
    
       
        $this->call->view('homepage', $data);
    }

    public function add_product_form() {
        $this->call->view('product/add_product');  
    }

    public function add_product() {
        if ($this->form_validation->submitted()) {
            $data = [
                'name' => $this->io->post('name'),
                'price' => $this->io->post('price'),
                'description' => $this->io->post('description')
            ];
            if ($this->product_model->insert_product($data)) {
                redirect('home');
            } else {
                echo "Error adding product.";
            }
        }
    }
    
    public function update_product($id) {
        if ($this->form_validation->submitted()) {
            
            if ($this->form_validation->run()) {
                
                $name = $this->io->post('name');
                $price = $this->io->post('price');
                $description = $this->io->post('description');
                
                
                if ($this->product_model->update_product($name, $price, $description, $id, [
                    'name' => $name,
                    'price' => $price,
                    'description' => $description
                ])) {
                    
                if (!function_exists('set_flash_alerts')) {
                    function set_flash_alerts($type, $message) {
                       
                        $CI = &get_instance();
                        
                       
                        $CI->session->set_flashdata('alert_type', $type);
                        $CI->session->set_flashdata('alert_message', $message);
                    }
                    redirect('home');
                }
                }
            } else {
                
                set_flash_alerts('danger', $this->form_validation->errors());
                redirect('home');
            }
        }
        
        
        $data['product'] = $this->product_model->get_product($id);
        $this->call->view('product/update', $data);
    }
    
    

    public function delete($id) {
        
        if ($this->product_model->delete($id)) {
            
            set_flash_alert('success', 'Product was deleted successfully');
        } else {
            
            set_flash_alert('danger', 'Something went wrong while deleting the product');
        }
    
        
        redirect('home');
    }
    
}
?>
