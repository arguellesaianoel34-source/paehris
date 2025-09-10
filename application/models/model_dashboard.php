<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_dashboard extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function count_hit_month()
    {
        $current_month = date('m');
        $current_year = date('Y');
        
        $sql = "SELECT 
                    DAY(datecreated) as day,
                    COUNT(*) as hit_count
                FROM prime_module_users_logs 
                WHERE MONTH(datecreated) = ? 
                AND YEAR(datecreated) = ?
                GROUP BY DAY(datecreated)
                ORDER BY day ASC";
        
        $query = $this->db->query($sql, array($current_month, $current_year));
        
        if ($query && $query->num_rows() > 0) {
            $result = array();
            foreach ($query->result() as $row) {
                $result[] = array(
                    'day' => $row->day,
                    'hits' => $row->hit_count
                );
            }
            return json_encode($result);
        }
        
        return json_encode(array());
    }
}
?>