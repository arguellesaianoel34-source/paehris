<?php

?>
<form action="<?php echo base_url();?>cad/updatereferral" method="post">
    <input type="hidden" name="referral_personid" id="personid">
    <div class="modal-body">
        <div class="form-group row">
            <div class="col-md-4">
                <input name="referral_lastname" id="lastname" class="form-control" placeholder="Last Name..." />
            </div>
            <div class="col-md-5">
                <input name="referral_firstname" id="firstname" class="form-control" placeholder="First Name..." />
            </div>
            <div class="col-md-3">
                <input name="referral_middlename" id="middlename" class="form-control" placeholder="Middle Name..." />
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-5">
                <input name="referral_contact" id="contact" class="form-control" placeholder="Contact" />
            </div>
            <div class="col-md-7">
                <input name="referral_email" id="email" class="form-control" type="email" placeholder="Email" />
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
</form>

<script>

    var personid = $('#personid', document);
    var lastname = $('#lastname', document);
    var firstname = $('#firstname', document);
    var middlename = $('#middlename', document);

    var a = new Bloodhound({
        datumTokenizer: function (e) {
            return e.tokens
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {url: PECO.base_url() + "search/personsearch?query=%QUERY", wildcard: "%QUERY"}
    });

    a.initialize(), lastname.typeahead(null, {
        hint: false,
        highlight: true,
        minLength: 1,
        displayKey: "lastname",
        source: a.ttAdapter(),
        cache: false,
        templates: {
            suggestion: Handlebars.compile(['<div class="media">', '<div class="pull-left">', '<div class="media-object">', '<img src="{{img}}" width="50" height="50"/>', "</div>", "</div>", '<div class="media-body">', '<h5 class="media-heading text-primary"><b class="text-glow-yellow">{{lastname}}</b>, {{firstname}} {{middlename}}</h5>', "<p>{{district}} - {{addr}}</p>", "</div>", "</div>"].join("")),
        },
    }).on('typeahead:selected', function(event, selection) {
        personid.val(selection.empid);
        firstname.text(selection.firstname);
        middlename.text(selection.middlename);
    }).click(function() {
        PECO.initElScroller($('.tt-dropdown-menu', document));
    });


</script>