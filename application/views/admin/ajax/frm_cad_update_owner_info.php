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
$dataid         = $this->input->post('ids');
$info           = get_application_details($dataid)->info;
$personid       = $info ? $info->personid : '';
$firstname      = $info ? $info->firstname : '';
$lastname       = $info ? $info->lastname : '';
$middlename     = $info ? $info->middlename : '';
$suffix         = $info ? $info->suffix : '';
$marital        = $info ? $info->marital : '';
$address        = ($info) ? $info->addrspec : '';
$country        = ($info && $info->country != 0) ? $info->country : 175;
$region         = ($info) ? $info->region : '';
$citymun        = ($info) ? $info->city : '';
$province       = ($info) ? $info->province : '';
$datecreated    = ($info) ? $info->datecreated : '';
$tinno          = ($info) ? $info->tinno : '';
$mapupdated     = ($info) ? $info->mapupdated : '';
$mapupdatedby   = ($info) ? $info->mapupdatedby : '';
$maplink        = ($info->geolink) ? $info->geolink : '';
$distid         = ($info) ? $info->distid : 0;
$essrno         = ($info) ? $info->essrno : false;
$moduleid       = ($info) ? $info->moduleid : 0;
$apptype        = ($info) ? $info->apptype : 0;
$phone          = (($info && $info->contactphone != '') || ($info->contactphone > 0)) ? $info->contactphone : '';
$mobile         = (($info && $info->contactmobile != '') || ($info->contactmobile > 0)) ? $info->contactmobile : '';
$email          = (($info && $info->contactemail != '') || ($info->contactemail > 0)) ? $info->contactemail : '';



//$check_contract = $this->model_cad->check_contract($dataid);


?>
<div class="tabbable-line pull-right" style="padding-top: 0px !important; padding-bottom: 15px !important;">
    <ul class="nav nav-tabs ">
        <li class="active">
            <a href="#owner_update" data-toggle="tab" aria-expanded="true" data-id="2"> Update Owner </a>
        </li>
        <li class="">
            <a href="#owner_ar_list" data-toggle="tab" aria-expanded="true" data-id="1"> Owners and Authorized Representatives</a>
        </li>
    </ul>

</div>
<div class="tab-content">
    <div class="tab-pane fade in active" id="owner_update">
        <div class="row" >
            <div class="col-md-12">
                <div class="row">
                    <form role="form" class="form-horizontal asset-entry-form"  action="<?php echo base_url(); ?>cad/updateapplicationownerinfo" method="post" id="frm_newaccount">
                        <input type="hidden" name="appid" id="input_appid" value="<?php echo $dataid; ?>"/>
                        <div class="col-md-12">
                            <div class="portlet light bordered" id="">

                                <div class="portlet-title">

                                    <div class="btn-group pull-right">
                                        <button class="btn btn-default inline" id="btn_new_owner" type="button" style="margin-right: 20px;"><i class="fa fa-edit"></i> New</button>
                                        <button class="btn btn-danger inline hidden" id="btn_cancel_newowner" type="button" style="margin-right: 20px;"><i class="fa fa-times"></i> Cancel</button>
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
                                        if ($apptype != 1) {
                                        ?>
                                        <tr>
                                            <td>
                                                <div id="non_residential" style="display: block;">
                                                    <div class="form-group margin-top-10" id="non_res_details">
                                                        <label class="col-md-2 control-label"><span class="required"></span> Establishment</label>
                                                        <div class="col-md-7"><input name="corpname" type="text" class="form-control data-entry input-lg" id="corpname" placeholder="Establishment name..." data-toggle="autocomplete" col-name="corpname" value="">
                                                            <div class="form-control-focus"> </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input name="corpbranch" type="text" class="form-control data-entry input-lg" id="corpbranch" placeholder="Branch" data-toggle="autocomplete" col-name="corpbranch" value="">
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
                                                <div class="col-md-3 select2-data-flat">
                                                    <input name="lastname" type="text" class="form-control data-entry" id="lastname" placeholder="Last Name" data-toggle="autocomplete" col-name="lastname" value="<?php echo $lastname;?>">
                                                    <div class="form-control-focus"> </div>
                                                    <span class="help-block">Last name</span> </div>
                                                <div class="col-md-3">
                                                    <input name="firstname" type="text" class="form-control data-entry" id="firstname" placeholder="First Name" data-toggle="autocomplete" col-name="firstname" value="<?php echo $firstname;?>">
                                                    <div class="form-control-focus"> </div>
                                                    <span class="help-block">First Name</span> </div>
                                                <div class="col-md-2">
                                                    <input name="middlename" type="text" class="form-control data-entry" id="middlename" placeholder="Middle Name" data-toggle="autocomplete" col-name="middlename" value="<?php echo $middlename;?>">
                                                    <div class="form-control-focus"> </div>
                                                    <span class="help-block">Middle Name</span> </div>

                                                <div class="col-md-2">
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
    </div>
    <div class="tab-pane fade in " id="owner_ar_list">
        <table class="table table-bordered table-condensed table-sm">
            <thead>
            <th>#</th>
            <th>Name</th>
            <th>Address</th>
            <th><i class="fa fa-sliders bold"></i> </th>
            </thead>
            <tbody>

            </tbody>
        </table>
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

<!-- END PAGE LEVEL PLUGINS -->

<script src="<?php echo base_url(); ?>assets/pages/cad/newaccount.js"></script>
<script src="<?php echo base_url(); ?>assets/global/scripts/address.js"></script>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/pages/cad/form-editable.js"></script>


<script type="text/javascript">
    $(document).find('select').each(function () {
        $(this).select2();
    });

    CAD.application();
    ADDRESS.init(<?php echo $country; ?>,<?php echo $region; ?>,<?php echo $province; ?>,<?php echo $citymun; ?>);
</script>
