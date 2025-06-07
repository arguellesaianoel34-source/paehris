<?php
echo "<pre>";
print_r ();
echo "</pre>";

?>

<form method="post" action="<?php echo base_url(); ?>cad/getdocumentpreview">
    <input name="id">
    <input name="doctype">
    <button type="submit">Send</button>
</form>
