<link rel="stylesheet" href="<?php echo base_url();?>/assets/global/plugins/icheck/skins/all.css">
<style>
    #tbl_requirements_list_filter {
        display: inline-block !important;
        width: 150px;
    }
    #tbl_requirements_list_filter input{
        width: 100% !important;
    }
</style>
<?php

[$dataid, $level] = explode(',',$this->input->post('ids'));

$info           = get_application_details($dataid)->info ?? null;
$personid       = $info->personid   ?? '';
$firstname      = $info->firstname  ?? '';
$lastname       = $info->lastname   ?? '';
$middlename     = $info->middlename ?? '';
$suffix         = $info->suffix     ?? '';
$marital        = $info->marital    ?? '';
$address        = $info->addrspec   ?? '';
$country        = (isset($info->country) && $info->country != 0) ? $info->country : 175;
$region         = $info->region     ?? '';
$citymun        = $info->city       ?? '';
$province       = $info->province   ?? '';
$datecreated    = $info->datecreated?? '';
$tinno          = $info->tinno      ?? '';
$mapupdated     = $info->mapupdated ?? '';
$mapupdatedby   = $info->mapupdatedby ?? '';
$maplink        = $info->geolink    ?? '';
$distid         = $info->distid     ?? 0;
$essrno         = $info->essrno     ?? false;
$moduleid       = $info->moduleid   ?? 0;
$apptype        = $info->apptype    ?? 0;
$phone          = (!empty($info->contactphone)) ? $info->contactphone : '';
$mobile         = (!empty($info->contactmobile)) ? $info->contactmobile : '';
$email          = (!empty($info->contactemail)) ? $info->contactemail : '';

//GET CORP INFO IF APPTYPE > 1
$corpname       = 'Unknown';
$corpbranch     = '';
$rf_contacts    = [];

$qry_corp_app = $this->db->select()
    ->from('application_customers_corporation')
    ->where(['appid' => $dataid, 'types' => $apptype])
    ->get()->row();

if($qry_corp_app) {
    $info = [];

    switch ($apptype) {
        case 2:
            $corpinfo = get_corporation_info($qry_corp_app->corpid);
            $pic_dir = 'corporation';
            break;
        default:
            $corpinfo = get_government_info($qry_corp_app->corpid);
            $pic_dir = 'government';
            break;
    }
    
    $corpid = $qry_corp_app->corpid;
    if ($corpinfo->qry) {
        $corpname = $corpinfo->info->descs;

        if($apptype == 2) {
            $qry_branch = $this->db->select()
                ->from('corporation_branches')
                ->where(['corpid' => $qry_corp_app->corpid, 'sysid' => $qry_corp_app->branchid])
                ->get()->row();
            if ($qry_branch) {
                $branchid = $qry_branch->sysid;
                $corpbranch = $qry_branch->names;
            }
        }else{
            $corpbranch = $corpinfo ? $corpinfo->info->names : '';
        }
    }
}

$referral = $this->db->select('p.sysid AS personid,p.firstname,p.lastname,p.middlename,pt.titleid AS title ')
    ->from('application_customers_referrals AS r')
    ->join('person AS p','r.personid = p.sysid','left')
    ->join('person_title AS pt','p.sysid = pt.personid','left')
    ->where(['r.appid' => $dataid, 'r.status' => 1])->get()->row();

if ($referral) {
    $referral_contacts = $this->db->select('MAX(CASE WHEN (types = 1051) THEN contactstring ELSE NULL END) AS mobile,MAX(CASE WHEN (types = 1049) THEN contactstring ELSE NULL END) AS phone')
        ->from('person_contact_matrix')
        ->where(['personid' => $referral->personid, 'status' => 1])
        ->where_in('types', [1049, 1051])->group_by('personid')->get()->row();

    if ($referral_contacts) {
        $rf_contacts = $referral_contacts;
    }
}
//$check_contract = $this->model_cad->check_contract($dataid);


?>

<div class="row" >
    <div class="col-md-12">
        <div class="row">
            <form role="form" class="form-horizontal asset-entry-form"  action="<?php echo base_url(); ?>cad/updateapplicationcustomerinfo" method="post" id="frm_newaccount">
                <input type="hidden" name="appid" id="input_appid" value="<?php echo $dataid; ?>"/>
                <input type="hidden" name="apptype" id="input_apptype" value="<?php echo $apptype; ?>"/>
                <div class="col-md-12">
                    <div class="portlet light bordered" id="">

                        <div class="portlet-title">
                            <?php if (!$essrno || $essrno == 0) { ?>
                            <label class="col-md-2 control-label person-name" for="name">TSSR <span class=""></span></label>
                            <div class="col-md-3 select2-data-flat">
                                <input name="tssr" type="text" class="form-control data-entry" id="tssr" placeholder="TSSR #" />
                                <div class="form-control-focus"> </div>
                            </div>
                            <?php } ?>
                            <div class="btn-group pull-right">
                                <button class="btn btn-primary" id="btn_update_owner" type="submit"><i class="fa fa-check"></i> Update</button>
                            </div>
                            <div class="caption">

                            </div>

                        </div>
                        <div class="portlet-body form">
                            <div id="query-status"></div>
                            <table class="table table-hover table-striped">
                                <tbody>
                                <?php
                                if ($apptype == 0 && $level <= 4) {
                                    ?>
                                    <tr>
                                        <td>
                                            <label class="col-md-2 control-label person-name" for="name">Type <span class="required"></span></label>
                                            <div class="col-md-10">
                                                <div class="row" id="apptype_row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="form-label col-md-1 center"> <span class=""></span></label>
                                                            <div class="col-md-11">
                                                                <div class="icheck-inline">
                                                                    <div class="row">

                                                                        <div class="col-md-3">
                                                                            <label><input name="apptype" value="0" type="radio" data-radio="iradio_square-red" class="icheck" /> N/A</label>
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <label><input name="apptype" value="1" type="radio" data-radio="iradio_square-red" class="icheck" /> Residential</label>
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <label><input name="apptype" value="2" type="radio" data-radio="iradio_square-red" class="icheck" /> Commercial</label>
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <label><input name="apptype" value="3" type="radio" data-radio="iradio_square-red" class="icheck" /> Government</label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                                <?php
                                if ($apptype > 1) {
                                    ?>
                                    <tr>
                                        <td>
                                            <div id="non_residential" style="display: block;">
                                                <input type="hidden" name="corpid" value="<?php echo $corpid; ?>">
                                                <input type="hidden" name="branchid" value="<?php echo $branchid ?? ''; ?>">
                                                <div class="form-group margin-top-10" id="non_res_details">
                                                    <label class="col-md-2 control-label"><span class="required"></span> Establishment</label>
                                                    <div class="col-md-7"><input name="corpname" type="text" class="form-control data-entry input-lg" id="corpname" placeholder="Establishment name..." data-toggle="autocomplete" col-name="corpname" value="<?php echo $corpname;?>">
                                                        <div class="form-control-focus"> </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input name="corpbranch" type="text" class="form-control data-entry input-lg" id="corpbranch" placeholder="Branch" data-toggle="autocomplete" col-name="corpbranch" value="<?php echo $corpbranch;?>">
                                                        <div class="form-control-focus"> </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                                <tr>
                                    <td>
                                        <label class="col-md-2 control-label person-name" for="name">Name <span class="required"></span></label>
                                        <input type="hidden" id="personid" name="personid" value="<?php echo $personid;?>">
                                        <div class="col-md-10">
                                            <div class="row">
                                                <div class="col-md-12" id="non_residential">

                                                </div>
                                                <div id="person_info">
                                                    <div class="col-md-3 select2-data-flat">
                                                        <input name="lastname" type="text" class="form-control data-entry" id="lastname" placeholder="Last Name" data-toggle="autocomplete" col-name="lastname" value="<?php echo $lastname;?>">
                                                        <div class="form-control-focus"> </div>
                                                        <span class="help-block">Last name</span> </div>
                                                    <div class="col-md-3">
                                                        <input name="firstname" type="text" class="form-control data-entry" id="firstname" placeholder="First Name" data-toggle="autocomplete" col-name="firstname" value="<?php echo $firstname;?>">
                                                        <div class="form-control-focus"> </div>
                                                        <span class="help-block">First Name</span> </div>
                                                    <div class="col-md-3">
                                                        <input name="middlename" type="text" class="form-control data-entry" id="middlename" placeholder="Middle Name" data-toggle="autocomplete" col-name="middlename" value="<?php echo $middlename;?>">
                                                        <div class="form-control-focus"> </div>
                                                        <span class="help-block">Middle Name</span> </div>

                                                    <div class="col-md-3">
                                                        <select name="suffix"class="form-control data-entry" id="suffix">
                                                            <option value=""></option>
                                                            <?php foreach (select_person_title(70) as $row) {
                                                                $selected = ($row->sysid == $suffix) ? 'selected' : '';
                                                                ?>
                                                                <option value="<?php echo $row->sysid; ?>" <?php echo $selected; ?>><?php echo $row->names; ?> - <?php echo $row->descriptions; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                        <div class="form-control-focus"> </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <label class="col-md-2 control-label" for="ponumber">Status</label>

                                            <!--<div class="col-md-2">
                                            Prefix
                                            <select name="prefix" class="form-control data-entry" id="prefix">
                                                <option value=""></option>
                                                <?php foreach (select_person_title(71) as $row) { ?>
                                                    <option value="<?php echo $row->sysid; ?>"><?php echo $row->names; ?></option>
                                                <?php } ?>

                                            </select>
                                            <div class="form-control-focus"> </div>
                                            <span class="help-block"></span>
                                        </div>-->

                                            <div class="col-md-3">
                                                Gender
                                                <div class="form-control-focus">
                                                    <div class="md-radio-inline" id="md_gender">
                                                        <?php $gender = $info ? $info->gender : 0;?>
                                                        <div class="md-radio">
                                                            <input id="radio_male" name="gender" class="md-radiobtn" value="1" type="radio" <?php echo ($gender == 1) ? 'checked' : '' ?>>
                                                            <label for="radio_male"> <span class="inc"></span> <span class="check"></span> <span class="box"></span> Male </label>
                                                        </div>
                                                        <div class="md-radio">
                                                            <input id="radio_female" name="gender" class="md-radiobtn" value="2" type="radio" <?php echo ($gender == 2) ? 'checked' : '' ?>>
                                                            <label for="radio_female"> <span class="inc"></span> <span class="check"></span> <span class="box"></span> Female </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                Marital Status:
                                                <select name="marital" class="form-control data-entry" id="marital">
                                                    <option value=""></option>
                                                    <?php foreach (select_marital() as $row) {
                                                        $selected = ($row->sysid == $marital) ? 'selected' : '';
                                                        ?>
                                                        <option value="<?php echo $row->sysid; ?>" <?php echo $selected; ?>><?php echo $row->descriptions; ?></option>
                                                    <?php } ?>

                                                </select>
                                                <div class="form-control-focus"> </div>
                                                <span class="help-block"></span>
                                            </div>

                                        </div>
                                    </td>
                                </tr>
                                <tr  id="partner_info" class=" hidden animated fadeIn fast">
                                    <td>
                                        <div class="form-group">
                                            <label class="col-md-2 control-label" for="">Partner Name</label>
                                            <div class="col-md-3">
                                                <input placeholder="Last Name" class="form-control" id="partnerlname" name="partnerlname"/>
                                                <div class="form-control-focus"> </div>
                                                <span class="help-block"></span>
                                            </div>
                                            <div class="col-md-3">
                                                <input placeholder="First Name" class="form-control" id="partnerfname" name="parnerfname" />
                                                <div class="form-control-focus"> </div>
                                                <span class="help-block"></span>
                                            </div>
                                            <div class="col-md-2">
                                                <input placeholder="Middle Name" class="form-control" id="partnermname" name="partnermname" />
                                                <div class="form-control-focus"> </div>
                                                <span class="help-block"></span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-group ">
                                            <label class="control-label col-md-2">Contacts <span class="required"></span>
                                            </label>
                                            <div class="col-md-3">
                                                <input type="text" class="form-control data-entry" id="phone" name="phone" placeholder="Ex: 3290002" value="<?php echo $phone;?>"/>
                                                <span class="help-block">
                                                            Provide your phone number </span>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="text" class="form-control data-entry" id="mobile" name="mobile" placeholder="Ex: 09179999988" value="<?php echo $mobile;?>"/>
                                                <span class="help-block">
                                                            Provide your mobile number </span>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="email" class="form-control data-entry" id="email" name="email" placeholder="Ex: yourname@email.com" value="<?php echo $email;?>"/>
                                                <span class="help-block">
                                                            Provide your email address </span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <label class="control-label col-md-2">Address <span class="required"></span>
                                            </label>


                                            <!--<label class="control-label col-md-3">City/Town <span class="required"></span>
                                            </label>-->
                                            <div class="col-md-5">
                                                <input id="select2_country" class="form-control" name="country" value="<?php echo $country;?>" />
                                                <span class="help-block">Country</span>
                                            </div>
                                            <div class="col-md-5">
                                                <input id="select2_region" class="form-control" name="region"  placeholder="Select region.." value="<?php echo $region;?>"/>
                                                <span class="help-block">Region</span>
                                            </div>
                                            <div class="col-md-5 col-md-offset-2">
                                                <input id="select2_province" class="form-control" name="province" placeholder="Select province.." value="<?php echo $province;?>"/>
                                                <span class="help-block">Province</span>
                                            </div>
                                            <div class="col-md-5">
                                                <input id="select2_citymun" class="form-control" name="city"  placeholder="Select Municipal / City.." value="<?php echo $citymun;?>"/>
                                                <span class="help-block">Municipal / City</span>
                                            </div>
                                            <div class="col-md-10 col-md-offset-2 margin-top-10">
                                                <textarea class="form-control" rows="2" id="addrspecific" name="addrspecific" placeholder="Ex: Blk9 Lot20, DECA Homes Subd., Red Gate, Near Security Guard Outpost"><?php echo $address;?></textarea>
                                                <span class="help-block">Input specific street address, blk, house number and landmark.</span>
                                            </div>
                                            <label class="control-label col-md-2">Google Map Location</span>
                                            </label>
                                            <div class="col-md-10">
                                                <div class="input-icon">
                                                    <i class="fa fa-map-marker"></i>
                                                    <input class="form-control" rows="3" id="addrgmap" name="googlemap" placeholder="Paste Google Map here!" value="<?php echo $maplink;?>"/>
                                                </div>
                                                <span class="help-block">Ex: https://www.google.com/maps/@10.8459772,122.6544582,11.75z</span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label class="col-md-2 control-label person-name" for="name">
                                            <!--<input name="referral" id="has_referral" value="1" type="checkbox" data-checkbox="icheckbox_flat-orange" class="icheck" data-target="#rf_person_info" />-->
                                            Referrer
                                        </label>
                                        <div class="col-md-10">
                                            <div id="referral_info">
                                                <input type="hidden" id="personid" name="rfpersonid" value="<?php echo $personid;?>">
                                                <div class="row">
                                                    <div class="col-md-3 select2-data-flat">
                                                        <input name="ref_lastname" type="text" class="form-control data-entry" id="ref_lastname" placeholder="Last Name" data-toggle="autocomplete" col-name="rflastname" value="<?php echo ($referral) ? $referral->lastname : ''; ?>">
                                                        <div class="form-control-focus"> </div>
                                                        <span class="help-block">Last name</span> </div>
                                                    <div class="col-md-3">
                                                        <input name="ref_firstname" type="text" class="form-control data-entry" id="ref_firstname" placeholder="First Name" data-toggle="autocomplete" col-name="rffirstname" value="<?php echo ($referral) ? $referral->firstname : '';?>">
                                                        <div class="form-control-focus"> </div>
                                                        <span class="help-block">First Name</span> </div>
                                                    <div class="col-md-3">
                                                        <input name="ref_middlename" type="text" class="form-control data-entry" id="ref_middlename" placeholder="Middle Name" data-toggle="autocomplete" col-name="rfmiddlename" value="<?php echo ($referral) ? $referral->middlename : '';?>">
                                                        <div class="form-control-focus"> </div>
                                                        <span class="help-block">Middle Name</span> </div>

                                                    <div class="col-md-3">
                                                        <select name="ref_suffix" class="form-control data-entry" id="ref_suffix">
                                                            <option value=""></option>
                                                            <?php foreach (select_person_title(70) as $row) {
                                                                $selected = ($referral && $row->sysid == $referral->title) ? 'selected' : '';
                                                                ?>
                                                                <option value="<?php echo $row->sysid; ?>" <?php echo $selected; ?>><?php echo $row->names; ?> - <?php echo $row->descriptions; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                        <div class="form-control-focus"> </div>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <input type="text" class="form-control data-entry" id="ref_phone_number" name="rfphone" placeholder="Ex: 3290002" value="<?php echo ($rf_contacts) ? $rf_contacts->phone : '';?>"/>
                                                        <span class="help-block">
                                                            Phone number </span>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="text" class="form-control data-entry" id="ref_mobile_number" name="rfmobile" placeholder="Ex: 09179999988" value="<?php echo ($rf_contacts) ? $rf_contacts->mobile : '';;?>"/>
                                                        <span class="help-block">
                                                            Mobile number </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <!-- gender -->
                        </div>
                    </div>
                </div>

            </form>

        </div>
    </div>
</div>



<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/moment.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/jquery.mockjax.js"></script>

<!--
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/js/fileinput.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/js/locales/fr.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/js/locales/es.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/themes/explorer/theme.js" type="text/javascript"></script>
-->

<!-- BEGIN PAGE LEVEL PLUGINS -->

<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/jquery-validation/js/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/jquery-validation/js/additional-methods.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/icheck/icheck.min.js"></script>
<!-- END PAGE LEVEL PLUGINS -->

<script src="<?php echo base_url(); ?>assets/pages/cad/newaccount.js"></script>
<script src="<?php echo base_url(); ?>assets/global/scripts/address.js"></script>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/pages/cad/form-editable.js"></script>


<script type="text/javascript">
    $(document).find('select').each(function () {
        $(this).select2();
    });

    $('.icheck-inline .icheck', $('#apptype_row',document)).each(function(){
        $(this).iCheck({
            checkboxClass: 'icheckbox_square-red',
            radioClass: 'iradio_square-red',
            increaseArea: '-10%'
        }).on('ifChecked', function(){
            var this_ = $(this);
            this_.attr('checked', true);
            var target = this_.attr('data-target');
            $(target).attr('disabled',false);
        }).on('ifUnchecked', function(){
            var this_ = $(this);
            this_.attr('checked', false);
            var target = this_.attr('data-target');
            $(target).attr('disabled',true);
        });
    });

    CAD.application();
    ADDRESS.init(<?php echo $country; ?>,<?php echo $region; ?>,<?php echo $province; ?>,<?php echo $citymun; ?>);
</script>
