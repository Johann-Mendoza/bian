<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

require_once APP_DIR . '../scheme/kernel/Model.php';

class Product_model extends Model {
    
    public function insert_product($data) {
        return $this->db->table('products')->insert($data);
    }

    public function get_all_products() {
        return $this->db->table('products')->get_all();
    }

    public function get_product($id) {
        return $this->db->table('products')->where('id', $id)->get();
    }

    public function update_product($name, $price, $description, $id) {
        $data = array(
            'name' => $name,
            'price' => $price,
            'description' => $description
        );
        
        return $this->db->table('products')->where('id', $id)->update($data);
    }
           

    public function delete($id) {
       
        return $this->db->table('products')->where('id', $id)->delete();
    }
    
    
    
}
