<?php

defined('BASEPATH') OR exit('No direct script access allowed');
class Model_customerprofile extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function get_customer_details($customer_id)
    {
        $query = $this->db->query("SELECT
              cd.sysid AS app_id,
              IF ( c.sysid IS NOT NULL, 'YES', 'NO' ) AS is_corporate,
              IF ( c.sysid IS NOT NULL, IF ( c.descs IS NULL, 'UNKNOWN', c.descs ), CONCAT( p.firstname, ' ', p.lastname ) ) AS app_name,
              cd.personid AS person_id,
              c.sysid AS corp_id,
              c.descs AS corp_name,
              cd.addrspec AS address,
              s.description AS status,
              cd.`status` AS status_id,
              cd.datecreated AS date_created,
              cd.dateupdated AS date_updated,
              cd.contactmobile AS contact_mobile,
              cd.contactphone AS contact_phone,
              cd.jobtype AS job_type,
              cd.aveusage AS average_usage,
              cd.avebill AS average_bill,
              cd.bill,
              cd.types,
              IF ( c.sysid IS NOT NULL, CONCAT( p2.firstname, ' ', p2.lastname ), NULL ) AS corp_person,
              country.country,
              address_region.descs AS region,
              address_province.descs AS province,
              address_city.`names` AS city,
              acg.lat AS latitude,
              acg.lon AS longitude,
              acg.url AS map_url,

              -- SYSTEM SIZE --
              acss.sysize AS system_size,
              acss.l1l2 AS l1_l2,
              acss.l1l3 AS l1_l3,
              acss.l2l3 AS l2_l3,
              acss.l1g AS l1_g,
              acss.l2g AS l2_g,
              acss.l3g AS l3_g,
              acss.l1l2a AS l1_l2_a,
              acss.l1l3a AS l1_l3_a,
              acss.l2l3a AS l2_l3_a,
              acss.power AS system_power,
              acss.nop AS number_of_panels,
              rcs.descs AS rate_class,
              spt.descs AS panel_type,
              acss.roofinclination AS roof_inclination,
              acss.inspectiondate AS inspection_date,
              acss.remarks AS system_size_remarks
              -- END SYSTEM SIZE --

            FROM application_customers_details AS cd
              LEFT JOIN application_customers_corporation AS cc ON cd.sysid = cc.appid
              LEFT JOIN corporation AS c ON cc.corpid = c.sysid
              LEFT JOIN person AS p ON cd.personid = p.sysid
              LEFT JOIN prime_system_status_parameter AS s ON cd.`status` = s.sysid
              LEFT JOIN corporation_representative AS cp ON c.sysid = cp.corpid
              LEFT JOIN person AS p2 ON cp.personid = p2.sysid
              LEFT JOIN address_country AS country ON cd.country = country.sysid
              LEFT JOIN address_region ON cd.region = address_region.sysid
              LEFT JOIN address_province ON cd.province = address_province.sysid
              LEFT JOIN address_city ON cd.city = address_city.sysid 
              LEFT JOIN application_customers_geodata AS acg ON cd.sysid = acg.appid
              
              -- SYSTEM SIZE --
              LEFT JOIN application_customers_system_size AS acss ON cd.sysid = acss.appid
              LEFT JOIN rate_class_specification AS rcs ON acss.rateclass = rcs.sysid
              LEFT JOIN solar_panel_types AS spt ON acss.paneltype = spt.sysid
              -- END SYSTEM SIZE --


            WHERE ( c.sysid IS NOT NULL OR p.sysid IS NOT NULL ) AND cd.sysid = ?
            GROUP BY IF ( p.sysid IS NOT NULL AND p.sysid > 0, p.sysid, c.sysid ) ",
            [$customer_id]);
        
        $documents = $this->db->query("SELECT *
            FROM application_customers_requirements AS acr
            LEFT JOIN prime_requirement_parameters AS prp ON prp.sysid = acr.reqid
            LEFT JOIN application_customers_attachments AS aca ON aca.attachmentid = acr.sysid
            WHERE acr.appid = ? AND acr.status = ? GROUP BY acr.sysid", [$customer_id, 1])
            ->result_array();

        $customer_arr = [
          'system_id' => $customer_id,
          'installation_date' => '',
          'system_type' => '',
          'panel_model' => '',
          'panel_quantity' => '',
          'total_capacity' => '',
          'invenrter_model' => '',
          'battery_storage' => '',
          'documents' => $documents,
          'billing' => [],
          'payment' => [],
          'services' => [],
        ];

        return array_merge($query->result_array()[0] ?? [], $customer_arr);

    }

    public function update_customer_details($customer_id, $data)
    {
        $this->db->where('sysid', $customer_id);
        return $this->db->update('application_customers_details', $data);
    }

    public function get_applications_with_details()
    {
        $sql = "SELECT
              cd.sysid AS app_id,
              IF ( c.sysid IS NOT NULL, 'YES', 'NO' ) AS is_corporate,
              IF ( c.sysid IS NOT NULL, IF ( c.descs IS NULL, 'UNKNOWN', c.descs ), CONCAT( p.firstname, ' ', p.lastname ) ) AS app_name,
              cd.personid AS person_id,
              c.sysid AS corp_id,
              c.descs AS corp_name,
              cd.addrspec AS address,
              s.description AS status,
              cd.`status` AS status_id,
              cd.datecreated AS date_created,
              cd.dateupdated AS date_updated,
              cd.contactmobile AS contact_mobile,
              cd.contactphone AS contact_phone,
              cd.jobtype AS job_type,
              cd.aveusage AS average_usage,
              cd.avebill AS average_bill,
              cd.bill,
              cd.types,
              IF ( c.sysid IS NOT NULL, CONCAT( p2.firstname, ' ', p2.lastname ), NULL ) AS corp_person,
              country.country,
              address_region.descs AS region,
              address_province.descs AS province,
              address_city.`names` AS city,
              acg.lat AS latitude,
              acg.lon AS longitude,
              acg.url AS map_url
            FROM application_customers_details AS cd
              LEFT JOIN application_customers_corporation AS cc ON cd.sysid = cc.appid
              LEFT JOIN corporation AS c ON cc.corpid = c.sysid
              LEFT JOIN person AS p ON cd.personid = p.sysid
              LEFT JOIN prime_system_status_parameter AS s ON cd.`status` = s.sysid
              LEFT JOIN corporation_representative AS cp ON c.sysid = cp.corpid
              LEFT JOIN person AS p2 ON cp.personid = p2.sysid
              LEFT JOIN address_country AS country ON cd.country = country.sysid
              LEFT JOIN address_region ON cd.region = address_region.sysid
              LEFT JOIN address_province ON cd.province = address_province.sysid
              LEFT JOIN address_city ON cd.city = address_city.sysid 
              LEFT JOIN application_customers_geodata AS acg ON cd.sysid = acg.appid
            WHERE ( c.sysid IS NOT NULL OR p.sysid IS NOT NULL ) 
            GROUP BY IF ( p.sysid IS NOT NULL AND p.sysid > 0, p.sysid, c.sysid ) 
            ORDER BY cd.datecreated DESC";

        
        $query = $this->db->query($sql);
        return $query->result_array();
    }
}
