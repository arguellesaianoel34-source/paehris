<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_pages extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_page_visit_month()
    {
        $current_month = date('m');
        $current_year = date('Y');
        
        $sql = "SELECT 
                    DATE(datecreated) as visit_date,
                    COUNT(*) as visit_count
                FROM prime_module_users_logs 
                WHERE MONTH(datecreated) = ? 
                AND YEAR(datecreated) = ?
                GROUP BY DATE(datecreated)
                ORDER BY visit_date ASC";
        
        $query = $this->db->query($sql, array($current_month, $current_year));
        
        if ($query && $query->num_rows() > 0) {
            $result = array();
            foreach ($query->result() as $row) {
                $result[] = array(
                    'date' => $row->visit_date,
                    'visits' => $row->visit_count
                );
            }
            return json_encode($result);
        }
        
        return json_encode(array());
    }
}
?>