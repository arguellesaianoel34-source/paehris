<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Model_customerprofile extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
    $this->load->database();
  }

  public function get_customer_details($customer_id)
  {
    /**
     * SELECT
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
            GROUP BY IF ( p.sysid IS NOT NULL AND p.sysid > 0, p.sysid, c.sysid ) 
     * 
     */
    $query = $this->db->query(
      "SELECT
        cam.sysid AS app_id,
        IF(cam.customertype = 2, 'YES','NO') AS is_corporate,
        IF(cam.customertype = 2,
          COALESCE(c.descs, CONCAT_WS(' ', p2.firstname, p2.lastname)),
          CONCAT_WS(' ', p.firstname, p.lastname)
        ) AS app_name,
        cam.personid AS person_id,
        c.sysid AS corp_id,
        c.descs AS corp_name,
        caa.addrspecific AS address,
        pssp.description AS status,
        cam.status AS status_id,
        cam.datecreated AS date_created,
        cam.dateupdated AS date_updated,

        -- prefer application_customers_details, else person/corporation contact matrices
        COALESCE(
          NULLIF(acd.contactmobile, ''),
          MAX(CASE WHEN LOWER(pt_person.names) LIKE '%mobile%' THEN pcm.contactstring END),
          MAX(pcm.contactstring),
          MAX(ccm.contactstring)
        ) AS contact_mobile,

        COALESCE(
          NULLIF(acd.contactphone, ''),
          MAX(CASE WHEN LOWER(pt_person.names) LIKE '%phone%' OR LOWER(pt_person.names) LIKE '%tel%' THEN pcm.contactstring END),
          MAX(CASE WHEN LOWER(pt_corp.names) LIKE '%phone%' OR LOWER(pt_corp.names) LIKE '%tel%' THEN ccm.contactstring END)
        ) AS contact_phone,

        ac.country,
        city.names AS city,
        acg.lat AS latitude,
        acg.lon AS longitude,
        acg.url AS map_url
      FROM customer_accounts_main cam
      LEFT JOIN customer_accounts_address caa ON cam.sysid = caa.acctid
      LEFT JOIN customer_accounts_geodata acg ON cam.appid = acg.appid
      LEFT JOIN person p ON cam.personid = p.sysid
      LEFT JOIN prime_system_status_parameter pssp ON cam.status = pssp.sysid
      LEFT JOIN corporation c ON cam.establishmentid = c.sysid
      LEFT JOIN corporation_representative cp ON c.sysid = cp.corpid
      LEFT JOIN person p2 ON cp.personid = p2.sysid
      LEFT JOIN application_customers_details acd ON cam.appid = acd.sysid
      LEFT JOIN address_country ac ON caa.country = ac.sysid
      LEFT JOIN address_city city ON caa.city = city.sysid

      -- contact matrices + types
      LEFT JOIN person_contact_matrix pcm ON pcm.personid = cam.personid
      LEFT JOIN prime_types_parameter pt_person ON pcm.types = pt_person.sysid
      LEFT JOIN corporation_contact_matrix ccm ON ccm.corpid = cam.establishmentid
      LEFT JOIN prime_types_parameter pt_corp ON ccm.types = pt_corp.sysid

      WHERE (cam.personid IS NOT NULL OR cam.establishmentid IS NOT NULL) AND cam.sysid = ?
      GROUP BY cam.sysid
      ORDER BY cam.datecreated DESC;
            ",
      [$customer_id]
    );

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
    // $sql = "SELECT
    //           cd.sysid AS app_id,
    //           IF ( c.sysid IS NOT NULL, 'YES', 'NO' ) AS is_corporate,
    //           IF ( c.sysid IS NOT NULL, IF ( c.descs IS NULL, 'UNKNOWN', c.descs ), CONCAT( p.firstname, ' ', p.lastname ) ) AS app_name,
    //           cd.personid AS person_id,
    //           c.sysid AS corp_id,
    //           c.descs AS corp_name,
    //           cd.addrspec AS address,
    //           s.description AS status,
    //           cd.`status` AS status_id,
    //           cd.datecreated AS date_created,
    //           cd.dateupdated AS date_updated,
    //           cd.contactmobile AS contact_mobile,
    //           cd.contactphone AS contact_phone,
    //           cd.jobtype AS job_type,
    //           cd.aveusage AS average_usage,
    //           cd.avebill AS average_bill,
    //           cd.bill,
    //           cd.types,
    //           IF ( c.sysid IS NOT NULL, CONCAT( p2.firstname, ' ', p2.lastname ), NULL ) AS corp_person,
    //           country.country,
    //           address_region.descs AS region,
    //           address_province.descs AS province,
    //           address_city.`names` AS city,
    //           acg.lat AS latitude,
    //           acg.lon AS longitude,
    //           acg.url AS map_url
    //         FROM application_customers_details AS cd
    //           LEFT JOIN application_customers_corporation AS cc ON cd.sysid = cc.appid
    //           LEFT JOIN corporation AS c ON cc.corpid = c.sysid
    //           LEFT JOIN person AS p ON cd.personid = p.sysid
    //           LEFT JOIN prime_system_status_parameter AS s ON cd.`status` = s.sysid
    //           LEFT JOIN corporation_representative AS cp ON c.sysid = cp.corpid
    //           LEFT JOIN person AS p2 ON cp.personid = p2.sysid
    //           LEFT JOIN address_country AS country ON cd.country = country.sysid
    //           LEFT JOIN address_region ON cd.region = address_region.sysid
    //           LEFT JOIN address_province ON cd.province = address_province.sysid
    //           LEFT JOIN address_city ON cd.city = address_city.sysid 
    //           LEFT JOIN application_customers_geodata AS acg ON cd.sysid = acg.appid
    //         WHERE ( c.sysid IS NOT NULL OR p.sysid IS NOT NULL ) 
    //         GROUP BY IF ( p.sysid IS NOT NULL AND p.sysid > 0, p.sysid, c.sysid ) 
    //         ORDER BY cd.datecreated DESC";

    $sql = "SELECT
  cam.sysid AS app_id,
  IF(cam.customertype = 2, 'YES','NO') AS is_corporate,
  IF(cam.customertype = 2,
     COALESCE(c.descs, CONCAT_WS(' ', p2.firstname, p2.lastname)),
     CONCAT_WS(' ', p.firstname, p.lastname)
  ) AS app_name,
  cam.personid AS person_id,
  c.sysid AS corp_id,
  c.descs AS corp_name,
  caa.addrspecific AS address,
  pssp.description AS status,
  cam.status AS status_id,
  cam.datecreated AS date_created,
  cam.dateupdated AS date_updated,

  -- prefer application_customers_details, else person/corporation contact matrices
  COALESCE(
    NULLIF(acd.contactmobile, ''),
    MAX(CASE WHEN LOWER(pt_person.names) LIKE '%mobile%' THEN pcm.contactstring END),
    MAX(pcm.contactstring),
    MAX(ccm.contactstring)
  ) AS contact_mobile,

  COALESCE(
    NULLIF(acd.contactphone, ''),
    MAX(CASE WHEN LOWER(pt_person.names) LIKE '%phone%' OR LOWER(pt_person.names) LIKE '%tel%' THEN pcm.contactstring END),
    MAX(CASE WHEN LOWER(pt_corp.names) LIKE '%phone%' OR LOWER(pt_corp.names) LIKE '%tel%' THEN ccm.contactstring END)
  ) AS contact_phone,

  ac.country,
  city.names AS city,
  acg.lat AS latitude,
  acg.lon AS longitude,
  acg.url AS map_url
FROM customer_accounts_main cam
LEFT JOIN customer_accounts_address caa ON cam.sysid = caa.acctid
LEFT JOIN customer_accounts_geodata acg ON cam.appid = acg.appid
LEFT JOIN person p ON cam.personid = p.sysid
LEFT JOIN prime_system_status_parameter pssp ON cam.status = pssp.sysid
LEFT JOIN corporation c ON cam.establishmentid = c.sysid
LEFT JOIN corporation_representative cp ON c.sysid = cp.corpid
LEFT JOIN person p2 ON cp.personid = p2.sysid
LEFT JOIN application_customers_details acd ON cam.appid = acd.sysid
LEFT JOIN address_country ac ON caa.country = ac.sysid
LEFT JOIN address_city city ON caa.city = city.sysid

-- contact matrices + types
LEFT JOIN person_contact_matrix pcm ON pcm.personid = cam.personid
LEFT JOIN prime_types_parameter pt_person ON pcm.types = pt_person.sysid
LEFT JOIN corporation_contact_matrix ccm ON ccm.corpid = cam.establishmentid
LEFT JOIN prime_types_parameter pt_corp ON ccm.types = pt_corp.sysid

WHERE (cam.personid IS NOT NULL OR cam.establishmentid IS NOT NULL)
GROUP BY cam.sysid
ORDER BY cam.datecreated DESC;";


    $query = $this->db->query($sql);
    return $query->result_array();
  }
}
